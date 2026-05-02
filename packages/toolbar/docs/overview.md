# Toolbar

Status: **Available, no schema impact** · Kind: **package** · Tier: **free** · Bundle: **foundation** · Contexts: **frontend, console** · Product group: **Capell Foundation**

This page is the consolidated implementation overview for the Toolbar package. It is extracted from the package README, service providers, migrations, config files, routes, resources, models, actions, and the shared Capell ERD notes where available.

## What This Plugin Adds

Toolbar adds a frontend admin toolbar beacon and pass-through activity middleware for Capell frontend pages.

- Frontend beacon route.
- Beacon controller and request.
- Pass-through activity middleware.
- Config flag for enabling the admin toolbar.

## Developer Notes

Provides a small frontend-to-admin signal that can be enabled without changing page models or layout data.

- ToolbarServiceProvider registers config and routes.
- Config file: capell-frontend-toolbar.php.
- Route: POST beacon, named capell-frontend.beacon.
- Middleware: PassThroughActivityMiddleware.
- Request: BeaconRequest.

## Operational Notes

Lets signed-in operators work from the frontend with admin context where the host app enables the toolbar.

- Adds frontend beacon route.
- Adds toolbar config key CAPELL_ADMIN_TOOLBAR.
- No database changes.
- No admin navigation.

## Data And Retention

- This package does not own data.
- Beacon handling depends on request context and host-app activity tracking.

## Screenshot Plan

- Frontend page with toolbar visible.
- Beacon network request.
- Toolbar enabled/disabled configuration proof.

## Pitfalls

- Disable the toolbar in environments where frontend admin controls should not appear.
- Beacon route is throttled at 60 requests per minute.
- CSRF is disabled for the beacon route; keep middleware and validation tight.

## Verification

- Run `vendor/bin/pest packages/toolbar/tests` when package tests exist.
- Run the relevant host-app migration or package install flow in a disposable database.
- Open the listed admin or frontend surface and compare it with the screenshot plan.

## Package Manifest

- Composer name: `capell-app/frontend-toolbar`
- Product group: Capell Foundation
- Kind: package
- Tier: free
- Bundle: foundation
- Contexts: `frontend`, `console`
- Requires: `capell-app/core`, `capell-app/frontend`
- Optional dependencies: None listed.

## Admin Surfaces

- None proven in this package directory.

## Commands

- None proven in this package directory.

## Routes And Config

- Config: packages/toolbar/config/capell-frontend-toolbar.php
- Route file: packages/toolbar/routes/web.php

## Permissions And Gates

- None proven in this package directory.

## Migrations

- None proven in this package directory.

## ERD Excerpt

This package has no committed ERD excerpt. Use implementation notes and extension points instead of inventing schema.

## Screenshot Automation

Deployment should read [screenshots.json](screenshots.json), install the package with demo data, resolve each admin surface or frontend URL, and write images to `public/docs/screenshots/packages/toolbar`.

- Frontend page with toolbar visible.
- Beacon network request.
- Toolbar enabled/disabled configuration proof.
