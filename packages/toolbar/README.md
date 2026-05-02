# Toolbar

Status: **Available, no schema impact** · Kind: **package** · Tier: **free** · Bundle: **foundation** · Contexts: **frontend, console** · Product group: **Capell Foundation**

## What This Plugin Adds

Toolbar adds a frontend admin toolbar beacon and pass-through activity middleware for Capell frontend pages.

- Frontend beacon route.
- Beacon controller and request.
- Pass-through activity middleware.
- Config flag for enabling the admin toolbar.

## Why It Matters

**For developers:** Provides a small frontend-to-admin signal that can be enabled without changing page models or layout data.

**For teams:** Lets signed-in operators work from the frontend with admin context where the host app enables the toolbar.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Frontend page with toolbar visible.
- Beacon network request.
- Toolbar enabled/disabled configuration proof.

## Technical Shape

- ToolbarServiceProvider registers config and routes.
- Config file: capell-frontend-toolbar.php.
- Route: POST beacon, named capell-frontend.beacon.
- Middleware: PassThroughActivityMiddleware.
- Request: BeaconRequest.

## Data Model

- This package does not own data.
- Beacon handling depends on request context and host-app activity tracking.

## Install Impact

- Adds frontend beacon route.
- Adds toolbar config key CAPELL_ADMIN_TOOLBAR.
- No database changes.
- No admin navigation.

## Commands

- None proven in this package directory.

## Admin And Access

- None proven in this package directory.

- None proven in this package directory.

## Common Pitfalls

- Disable the toolbar in environments where frontend admin controls should not appear.
- Beacon route is throttled at 60 requests per minute.
- CSRF is disabled for the beacon route; keep middleware and validation tight.

## Quick Start

1. Install the package with `composer require capell-app/frontend-toolbar`.
2. Register the package provider through Composer discovery and clear cached config if the host app uses config caching.
3. Open the new admin or frontend surface and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../authentication-log/README.md](../authentication-log/README.md)
- [../html-minify/README.md](../html-minify/README.md)
