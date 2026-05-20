# Access Gate

Status: **Available, schema-owning** · Kind: **package** · Tier: **premium** · Bundle: **operations** · Contexts: **admin, frontend, console** · Product group: **Capell Operations**

Access Gate adds gated access areas, request intake, claim-token flows, active grants, browser tokens, and audit events for protected Capell surfaces.

## What This Package Adds

- Filament resources for access areas, registrations, grants, claim tokens, browser tokens, and access events.
- Public request, claim, logout, and optional status routes under the configured access route prefix.
- Access-gate middleware and frontend rule conditions for page/layout gating.
- Configurable registration fields, identity methods, approval strategies, token policies, and rate limits.
- Install, setup, and doctor commands for host application maintenance.

## Install Flow

- Composer package: `capell-app/access-gate`
- Hard dependencies: `capell-app/core`
- Optional dependencies: `capell-app/public-actions`
- Run host migrations through the package install flow, then run `capell:extension-install capell-app/access-gate`.
- Run `capell:access-gate-doctor` in the host app to verify middleware order, route registration, and package health.

## Admin Surfaces

- `AccessAreaResource`: list, create, and edit access areas.
- `RegistrationResource`: list requests and run approve, reject, resend claim, expire, and grant actions.
- `GrantResource`: list grants and revoke active grants.
- `ClaimTokenResource`: list claim-token status.
- `BrowserTokenResource`: list browser-token status and revoke active browser tokens.
- `AccessGateEventResource`: list audit events.

## Frontend Surfaces

- `GET /access/request/{area}`: access request form.
- `POST /access/request/{area}`: access request submission endpoint.
- `GET /access/claim/{token}`: claim-token endpoint.
- `POST /access/logout/{area}`: area logout endpoint.
- Optional `GET /access/status/{area}` endpoint when enabled in config.
- Blade views render the request form, blocked message, and reusable request CTA.

Anonymous and non-admin public output must stay free of authoring markers, editor URLs, model IDs, and admin-only labels.

## Screenshot Plan

- Access areas admin index.
- Create/edit access area form.
- Registrations admin index with approval actions.
- Grants admin index with revoke action.
- Claim tokens admin index.
- Browser tokens admin index.
- Access events admin index.
- Public access request form.
- Public gated message.
- Public request CTA component.

## Known Risks

- The batch harness Composer install discovered the package and registered public routes, but `php artisan migrate --graceful` reported no migrations to run before `capell:extension-install`. Verify the package install flow publishes or runs all access-gate migrations in a fresh host app.
- Screenshots need seeded access areas, registrations, grants, and tokens before final publication.

## Feature Suggestions

- Add an admin “Access Area health” panel that previews request URL, status endpoint availability, registration policy, and middleware status for each area.
- Add a bulk registration triage workflow with filters for area, status, requested host, and one-click approve/reject notes.
- Add a safe public preview command that renders the request form and blocked message for a selected area without requiring a live protected page.
