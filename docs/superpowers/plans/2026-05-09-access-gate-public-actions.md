# Access Gate Public Actions Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extend Access Gate so a gated request can be triggered from a button or standalone URL, while moving reusable submit-triggered automation into a separate package that can be used by other Capell features and exposed through a native Zapier integration.

**Architecture:** `capell-app/access-gate` remains responsible for access areas, registrations, grants, claim links, browser tokens, and route/middleware protection. A new `capell-app/public-actions` package owns public action definitions, submitted payloads, dispatch attempts, outbound provider adapters, and safe public submit endpoints. Access Gate integrates with Public Actions by exposing an Access Gate action handler, not by embedding webhook/API automation logic inside the gate package.

**Tech Stack:** PHP 8.2, Laravel, Filament, Pest, Lorisleiva Actions, Spatie Laravel Data, Laravel package tools, Capell Core/Admin/Frontend, optional FormBuilder integration, queued jobs, Laravel HTTP client, signed public routes, encrypted adapter secrets, Zapier Platform CLI, TypeScript, Node.js 22-compatible Zapier code.

---

## Current State

- `packages/access-gate` currently gates whole routes through `AccessGateMiddleware`.
- Denied visitors are redirected to `/access/request/{area}`.
- `StoreAccessRequestController` submits the built-in email request form and calls `CreateRegistrationAction`.
- `CreateRegistrationAction` owns validation, registration persistence, event recording, auto-approval, claim token resend, and access request notification.
- Access Gate already has two useful extension patterns:
  - `AccessRequestMethodRegistry` for alternate request flows such as OAuth.
  - `RegistrationFieldRegistry` for package/app-owned registration fields.
- `packages/form-builder` already emits `FormSubmitted`.
- `packages/newsletter` already listens to `FormSubmitted` without hard-coupling to FormBuilder classes, which is the pattern to copy for optional package integration.

## Recommended Product Shape

Build this as two related pieces.

### 1. Access Gate Targeted Entry Points

Access Gate should support more than route middleware:

- A public request URL for an area.
- A POST endpoint that can be used by a button or form submit.
- A small Blade/Livewire-friendly render helper or component that outputs a CTA/form for an area.
- Optional redirect handling after submit.
- No outbound automation provider logic.

This keeps Access Gate boring and focused: "this user asked for access to this area."

### 2. Public Actions Package

Create `packages/public-actions` as a reusable package for "a public submit happened, now run configured behavior."

It should support:

- Named public actions, for example `access-gate.request`, `lead.capture`, `demo.request`, `download.claim`, or `contact.submit`.
- Action classes that receive typed payload data and return a typed result.
- Optional persisted submissions for audit/debugging.
- Dispatch attempts with status, response body summary, retry count, and error message.
- Provider adapters, starting with a generic signed HTTP webhook adapter.
- Optional adapters/presets for Zapier, Make, Pipedream, and n8n, implemented as config presets over the same HTTP adapter unless a provider needs special behavior.
- A native Zapier integration so Zapier users can authenticate to a Capell site, use Capell submissions as Zap triggers, and submit configured Public Actions as Zap actions.

The first adapter should be generic HTTP webhook. Zapier, Pipedream, Make, and n8n all support webhook/HTTP-triggered workflows, so we do not need bespoke business logic in Capell for the first version.

The native Zapier integration is still worth building because it gives Capell a cleaner product surface than asking every user to paste Catch Hook URLs. The webhook preset remains the fast outbound option; the Zapier integration becomes the polished two-way option.

## External Automation Notes

Use an outbound webhook model first:

- Zapier Webhooks can receive GET, PUT, or POST requests through Catch Hook/Catch Raw Hook URLs and expose submitted fields to later Zap steps.
- Pipedream HTTP triggers expose a URL and run the workflow on each request.
- n8n Webhook nodes provide test and production URLs, support standard HTTP methods, and can require Basic, header, or JWT authentication.
- Make instant triggers run when webhook data arrives and expose the webhook payload to the scenario.

Recommended first-class adapter: `HttpWebhookPublicActionAdapter`.

Recommended provider presets:

- `generic`: arbitrary endpoint, headers, method, JSON body.
- `zapier`: POST JSON to Catch Hook URL, no custom response expectation.
- `pipedream`: POST JSON to workflow endpoint, accept default/custom 2xx response.
- `n8n`: POST JSON to production webhook URL, optional header/basic/JWT auth.
- `make`: POST JSON to custom webhook URL.

## Native Zapier Integration

Build a Zapier Platform CLI integration under:

- `integrations/zapier/capell-public-actions`

Use TypeScript because Zapier recommends TypeScript for CLI integrations, and target Zapier's Node.js 22 runtime.

The integration should provide:

- Authentication: API key/header auth against a Capell-generated integration token.
- Trigger: `New Public Action Submission`
  - Polls Capell for recent submissions visible to the token.
  - Returns stable IDs, timestamps, action key, site key/name where safe, source type, and flattened payload values.
  - Does not return encrypted secrets, raw IP addresses, internal class names, or admin-only metadata.
- Create action: `Submit Public Action`
  - Lets a Zap submit data into a configured Capell Public Action.
  - Uses a dynamic dropdown of active action keys.
  - Returns submission ID, result status, message, and redirect URL when available.
- Search: `Find Public Action`
  - Lets Zapier populate action dropdowns and validate an action key.

Capell-side API endpoints needed by Zapier:

- `GET /api/public-actions/zapier/me`
  - Tests authentication and returns account/site label data.
- `GET /api/public-actions/zapier/actions`
  - Lists active Zapier-exposed actions.
- `POST /api/public-actions/zapier/actions/{action}/submissions`
  - Submits a Public Action from Zapier.
- `GET /api/public-actions/zapier/submissions`
  - Lists recent submissions for polling triggers, with cursor or `since` support.

The native integration should start private/invite-only. Public Zapier listing review can be a separate follow-up once usage, docs, support expectations, and API stability are proven.

## Package Boundaries

- Access Gate must not know about Zapier, Make, Pipedream, n8n, or any automation provider.
- Public Actions must not import Access Gate models directly for its core behavior.
- The optional bridge may live in Access Gate as an action class implementing a Public Actions contract, or in a tiny bridge namespace inside Public Actions that only activates when Access Gate exists. Prefer Access Gate owns the bridge because access registration is Access Gate domain logic.
- FormBuilder should not be rewritten. Public Actions can add a listener for `FormSubmitted` later, following Newsletter's loose integration pattern.
- No public frontend output should expose admin metadata, internal IDs, class names, package names, field paths, provider secrets, signed editor URLs, or adapter internals.

## Data Model

### Public Actions

Create:

- `public_actions`
  - `id`
  - `site_id` nullable
  - `key` unique per site scope
  - `name`
  - `status`
  - `handler_class`
  - `success_redirect_url` nullable
  - `failure_redirect_url` nullable
  - `success_message` nullable
  - `failure_message` nullable
  - `payload_schema` JSON nullable
  - `settings` JSON nullable
  - timestamps

- `public_action_destinations`
  - `id`
  - `public_action_id`
  - `adapter`
  - `name`
  - `status`
  - `endpoint_url` encrypted nullable
  - `secret` encrypted nullable
  - `headers` encrypted JSON nullable
  - `settings` JSON nullable
  - timestamps

- `public_action_submissions`
  - `id`
  - `public_action_id`
  - `site_id` nullable
  - `source_type` nullable
  - `source_id` nullable
  - `payload` encrypted JSON
  - `metadata` JSON
  - `status`
  - `submitted_at`
  - timestamps

- `public_action_dispatch_attempts`
  - `id`
  - `public_action_submission_id`
  - `public_action_destination_id` nullable
  - `adapter`
  - `status`
  - `attempt`
  - `request_hash`
  - `response_status` nullable
  - `response_summary` nullable
  - `error_message` nullable
  - `dispatched_at` nullable
  - timestamps

- `public_action_integration_tokens`
  - `id`
  - `site_id` nullable
  - `name`
  - `token_hash`
  - `provider`
  - `abilities` JSON
  - `last_used_at` nullable
  - `revoked_at` nullable
  - timestamps

### Access Gate

Avoid new Access Gate tables for the first slice. Add minimal fields to `access_gate_areas` only if admin configuration needs first-class UI:

- `public_action_key` nullable string
- `request_success_redirect_url` nullable string

If the Access Gate handler can be configured entirely in Public Actions, skip these columns and keep the relationship one-way: Public Action calls Access Gate.

## Contracts And Data

Create in `packages/public-actions/src`:

- `Contracts/PublicActionHandler.php`
  - `handle(PublicActionSubmissionData $submission): PublicActionResultData`

- `Contracts/PublicActionDestinationAdapter.php`
  - `dispatch(PublicActionDestination $destination, PublicActionSubmission $submission): PublicActionDispatchResultData`

- `Data/PublicActionPayloadData.php`
  - typed wrapper for submitted public values

- `Data/PublicActionMetadataData.php`
  - IP hash, user agent, URL, referer, route, site ID

- `Data/PublicActionSubmissionData.php`
  - action key, payload, metadata, source model reference

- `Data/PublicActionResultData.php`
  - success flag, message, redirect URL, created model reference

- `Data/PublicActionDispatchResultData.php`
  - success flag, response status, response summary, external ID, error message

- `Data/PublicActionZapierSubmissionData.php`
  - sanitized outbound representation for Zapier trigger polling

- `Data/PublicActionIntegrationTokenData.php`
  - plain token returned once at creation, token model, abilities

Create in Access Gate:

- `Actions/SubmitAccessGatePublicAction.php`
  - validates the action payload
  - resolves the Access Gate area
  - calls `CreateRegistrationAction`
  - returns `PublicActionResultData`

## Public Flow

### Button Submit

The frontend renders a form/button:

```blade
<form method="post" action="{{ route('capell-public-actions.submit', ['action' => 'preview-access']) }}">
    @csrf
    <input type="hidden" name="area" value="capell-preview">
    <input type="hidden" name="redirect" value="{{ url()->current() }}">
    <button type="submit">Request access</button>
</form>
```

The POST endpoint:

1. Resolves active `PublicAction` by key and site scope.
2. Builds `PublicActionSubmissionData`.
3. Runs the configured handler class.
4. Persists the submission when configured.
5. Dispatches configured destinations synchronously or queues them.
6. Redirects or returns JSON based on request type.

### Page URL

The frontend can link to:

```text
/actions/preview-access
```

The GET route renders a minimal public action page for actions that require input, or redirects to the POST-only target for single-click actions if the action is explicitly configured as GET-safe.

Default rule: state-changing actions require POST. GET can show a form, not create a registration.

## Admin UX

### Public Actions Resource

Editors/admins can configure:

- key
- name
- site scope
- status
- handler class from a registry, not arbitrary text where possible
- payload fields
- success/failure message
- success/failure redirect
- destinations

### Access Gate Resource

Add a "Public request action" section only if we decide Access Gate areas should expose first-class CTA configuration:

- enable targeted request action
- action key
- button label
- success message
- redirect URL

Use translations for every label.

## Implementation Tasks

### Task 1: Public Actions Skeleton

- [ ] Create `packages/public-actions/composer.json`.
- [ ] Create `packages/public-actions/capell.json`.
- [ ] Add PSR-4 autoload and test autoload entries to `composer.json` and `composer.local.json`.
- [ ] Create `PublicActionsServiceProvider`.
- [ ] Register package metadata with `CapellCore::registerPackage`.
- [ ] Register config, migrations, translations, routes, models, and Filament resources.
- [ ] Add provider smoke tests.
- [ ] Run `vendor/bin/pest packages/public-actions/tests --configuration=phpunit.xml`.

### Task 2: Public Actions Domain Model

- [ ] Add migrations for actions, destinations, submissions, and dispatch attempts.
- [ ] Add migration for `public_action_integration_tokens`.
- [ ] Add models with explicit casts and relationships.
- [ ] Add enums for action status, destination status, submission status, dispatch status, integration provider, and integration token ability.
- [ ] Add factories for each model.
- [ ] Add model tests for casts, relationships, encrypted fields, token hashing, revocation, and site-scoped uniqueness.
- [ ] Run the Public Actions model tests.

### Task 3: Handler And Adapter Contracts

- [ ] Add `PublicActionHandler`.
- [ ] Add `PublicActionDestinationAdapter`.
- [ ] Add data classes for payload, metadata, submission, handler result, and dispatch result.
- [ ] Add `PublicActionHandlerRegistry`.
- [ ] Add `PublicActionDestinationAdapterRegistry`.
- [ ] Add tests proving registries reject invalid classes and resolve valid classes.

### Task 4: Submit Endpoint

- [ ] Add `SubmitPublicActionController`.
- [ ] Add POST route with throttle middleware.
- [ ] Add GET route for rendering public action pages where configured.
- [ ] Add `SubmitPublicActionAction`.
- [ ] Ensure POST-only default for state-changing actions.
- [ ] Hash IP addresses in metadata instead of storing raw IP by default.
- [ ] Add feature tests for POST success, validation failure, inactive action, missing action, JSON response, redirect response, throttling, and no-store headers.

### Task 5: Generic HTTP Webhook Adapter

- [ ] Add `HttpWebhookPublicActionAdapter`.
- [ ] Use Laravel HTTP client.
- [ ] Support method, endpoint, headers, JSON body, timeout, and expected 2xx response.
- [ ] Redact secrets from logs and attempt summaries.
- [ ] Persist each dispatch attempt.
- [ ] Add retryable queued job for async destination dispatch.
- [ ] Add fake HTTP tests for success, provider failure, timeout, redaction, retry count, and idempotency hash.

### Task 6: Provider Presets

- [ ] Add config presets for `generic`, `zapier`, `pipedream`, `n8n`, and `make`.
- [ ] Keep presets as thin settings over the generic HTTP adapter.
- [ ] Add docs explaining that Capell sends JSON to the provider webhook and the provider owns downstream workflow logic.
- [ ] Add tests proving each preset resolves to the generic adapter with provider-specific defaults.

### Task 7: Access Gate Handler

- [ ] Add optional dependency checks so Access Gate can register its handler only when Public Actions exists.
- [ ] Create `SubmitAccessGatePublicAction`.
- [ ] Map payload keys to `CreateRegistrationAction` input:
  - `area`
  - `email`
  - `requested_url`
  - registered Access Gate field values
  - metadata from Public Actions
- [ ] Return a public action result containing the Access Gate registration reference and safe translated message.
- [ ] Add tests for successful registration, duplicate single-per-email registration, invite-only failure, paused area failure, custom field validation, and no provider coupling.

### Task 8: Access Gate CTA/Page Integration

- [ ] Add a small Blade component or helper for rendering a request CTA/form.
- [ ] Support direct URL rendering for a public action key.
- [ ] Keep current `/access/request/{area}` flow working unchanged.
- [ ] Add no-store headers to submit responses.
- [ ] Add tests proving a page can render a CTA without making the whole route gated.
- [ ] Add tests proving anonymous/non-admin page output does not expose admin metadata, package internals, class names, provider settings, or signed editor URLs.

### Task 9: Optional FormBuilder Bridge

- [ ] Add a Public Actions listener for FormBuilder's `FormSubmitted` event.
- [ ] Follow Newsletter's loose integration pattern: inspect the event object and model class names dynamically.
- [ ] Allow a form handle or form ID to map to a public action key.
- [ ] Add tests proving FormBuilder is optional and Public Actions boots without it installed.

### Task 10: Admin Resources

- [ ] Add Filament resources for public actions, destinations, submissions, and dispatch attempts.
- [ ] Add Filament management for Zapier/API integration tokens, with one-time token reveal on creation.
- [ ] Scope queries by site where the host app supports site assignment.
- [ ] Do not expose encrypted secrets in table columns or infolists.
- [ ] Add actions for enabling/disabling destinations and replaying failed dispatch attempts.
- [ ] Add actions for revoking integration tokens.
- [ ] Add admin tests for resource registration, site scoping, secret redaction, token one-time reveal, token revocation, and replay behavior.

### Task 11: Zapier API Surface

- [ ] Add `PublicActionZapierAuthMiddleware`.
- [ ] Add `ListZapierPublicActionsController`.
- [ ] Add `ShowZapierAccountController`.
- [ ] Add `SubmitZapierPublicActionController`.
- [ ] Add `ListZapierPublicActionSubmissionsController`.
- [ ] Add `CreatePublicActionIntegrationTokenAction`.
- [ ] Add `ResolvePublicActionIntegrationTokenAction`.
- [ ] Add `RevokePublicActionIntegrationTokenAction`.
- [ ] Add `BuildZapierSubmissionPayloadAction`.
- [ ] Add API routes under a neutral configurable prefix.
- [ ] Add feature tests for successful auth, failed auth, revoked token, ability checks, action listing, Zapier submission creation, submission polling, cursor handling, and payload redaction.

### Task 12: Zapier Platform CLI Integration

- [ ] Create `integrations/zapier/capell-public-actions/package.json`.
- [ ] Configure Zapier Platform CLI dependencies and TypeScript build/test scripts.
- [ ] Create `integrations/zapier/capell-public-actions/src/index.ts`.
- [ ] Create `src/authentication.ts` using API key/header authentication.
- [ ] Create `src/triggers/newPublicActionSubmission.ts`.
- [ ] Create `src/creates/submitPublicAction.ts`.
- [ ] Create `src/searches/findPublicAction.ts`.
- [ ] Create shared API client helpers that support a configurable Capell base URL and token.
- [ ] Add Zapier integration tests with mocked HTTP responses.
- [ ] Run the Zapier local test command from that integration directory.
- [ ] Use `zapier-platform invoke` locally for auth, trigger, search, and create once credentials are available.
- [ ] Keep the initial Zapier integration private/invite-only; do not promote to public listing in this implementation plan.

### Task 13: Documentation

- [ ] Update `packages/access-gate/README.md` with targeted request examples.
- [ ] Create `packages/public-actions/README.md`.
- [ ] Document the generic HTTP adapter and provider presets.
- [ ] Document the native Zapier integration setup separately from the Catch Hook preset.
- [ ] Document how to create/revoke Zapier integration tokens.
- [ ] Document the Access Gate handler payload.
- [ ] Add "when to use Access Gate vs FormBuilder vs Public Actions" guidance.
- [ ] Update root package docs if this repo has a product/package index.

### Task 14: Verification

- [ ] Run `COMPOSER=composer.local.json composer dump-autoload --no-scripts` if new namespaces are not autoloaded.
- [ ] Run `vendor/bin/pest packages/public-actions/tests --configuration=phpunit.xml`.
- [ ] Run `vendor/bin/pest packages/access-gate/tests --configuration=phpunit.xml`.
- [ ] Run any new FormBuilder bridge tests if Task 9 is included.
- [ ] Run the Zapier integration test command from `integrations/zapier/capell-public-actions`.
- [ ] Run `composer preflight` before commit.

## Suggested Build Order

1. Build Public Actions skeleton and models.
2. Build submit endpoint and handler registry with a fake handler.
3. Build generic HTTP webhook adapter.
4. Add Access Gate handler.
5. Add CTA/page integration.
6. Add admin UI.
7. Add Zapier API surface.
8. Add native Zapier CLI integration.
9. Add optional FormBuilder bridge.
10. Harden docs, tests, and preflight.

## Sources Checked For Provider Direction

- Zapier Webhooks: https://help.zapier.com/hc/en-us/articles/8496288690317-Trigger-Zaps-from-webhooks
- Zapier Platform CLI: https://docs.zapier.com/integrations/build-cli/overview
- Zapier TypeScript integrations: https://docs.zapier.com/platform/build-cli/typescript-integrations
- Pipedream HTTP/Webhook triggers: https://pipedream.com/docs/workflows/building-workflows/triggers
- n8n Webhook node: https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-base.webhook/
- Make instant webhook triggers: https://developers.make.com/custom-apps-documentation/app-components/modules/instant-trigger
