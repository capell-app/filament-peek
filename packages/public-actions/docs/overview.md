# Public Actions

Status: **Available, schema-owning** · Kind: **package** · Tier: **premium** · Bundle: **automation** · Contexts: **admin, frontend, console** · Product group: **Capell Automation**

Public Actions lets a Capell site expose configured public submission endpoints and dispatch those submissions to first-party handlers or outbound automation destinations.

## What This Package Adds

- Public Action, Destination, Submission, Dispatch Attempt, and Integration Token Filament resources.
- Public web submit route at `/{route_prefix}/{action}`, defaulting to `/actions/{action}`.
- Zapier/API routes under `/api/public-actions/zapier`.
- Webhook dispatch jobs, provider presets, token authentication, throttling, and durable dispatch attempts.
- Optional integration points for Access Gate and Form Builder.

## Install And Demo Setup

Install the package in a host Capell app:

```bash
composer require capell-app/public-actions
```

Run the host app's migration/package install flow, then seed at least one active public action and destination before capturing screenshots.

## Admin Surfaces

- `PublicActionResource`: action key, handler, site scope, redirect/messages, API/Zapier exposure, and payload schema.
- `PublicActionDestinationResource`: webhook destination, adapter, endpoint, secret, headers, and settings.
- `PublicActionSubmissionResource`: received public submissions and status.
- `PublicActionDispatchAttemptResource`: outbound dispatch attempts, response status, and retry state.
- `PublicActionIntegrationTokenResource`: provider-scoped integration tokens.

## Frontend Surfaces

- `GET /actions/{action}` renders the package public action form.
- `POST /actions/{action}` accepts a public submission with `throttle:public-actions-submit`.
- `GET /api/public-actions/zapier/me`, `/actions`, and `/submissions` expose Zapier discovery.
- `POST /api/public-actions/zapier/actions/{action}/submissions` accepts authenticated Zapier submissions.

## Screenshot Plan

- Public Actions admin index.
- Public Action create/edit form.
- Destinations admin index and destination form.
- Submissions admin index.
- Dispatch attempts admin index.
- Integration token list and create-token modal.
- Public action frontend form.
- Zapier API discovery response.

## Public Safety Notes

Treat public routes as untrusted input. Screenshots of public forms should not expose handler class names, secrets, integration token values, internal model IDs, or admin-only labels.

## Verification

```bash
vendor/bin/pest packages/public-actions/tests --configuration=phpunit.xml
```
