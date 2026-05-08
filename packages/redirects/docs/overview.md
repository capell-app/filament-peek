# Redirects

Status: **Available, schema-owning** · Kind: **package** · Tier: **free** · Bundle: **foundation** · Contexts: **frontend** · Product group: **Capell Foundation**

This page is the consolidated implementation overview for the Redirects package. It is extracted from the package README, service providers, migrations, config files, routes, resources, models, actions, and the shared Capell ERD notes where available.

## What This Plugin Adds

Redirects adds automatic redirect creation from changed page URLs, frontend redirect resolution, and redirect health snapshots.

- Automatic redirect creation action.
- Page URL redirect recorder and resolver support.
- Redirect health snapshot actions and model.

## Developer Notes

Provides resolver and recorder contracts so frontend code can resolve redirects without coupling to one implementation.

- RedirectsServiceProvider registers the package.
- Config file: redirects.php.
- Migration creates redirect_health_snapshots.
- Listener creates redirects for changed page URLs.

## Operational Notes

Helps site operators preserve traffic and search value when URLs change.

- Adds redirect_health_snapshots table.
- Adds config for automatic redirects and status code.
- No package route file is present.
- Can create redirects when page URLs change.

## Data And Retention

- redirect_health_snapshots stores redirect health results.
- Redirect records appear to integrate with core page URL redirect behaviour rather than a package-owned redirects migration in this package.
- Deletion and retention for health snapshots should be verified against site operations policy.

## Screenshot Plan

- Redirect health snapshot output.

## Pitfalls

- Confirm where redirect records are stored in the host app before recording automatic redirects.
- Keep automatic redirects enabled only when changed page URLs should produce 301s.
- Validate redirect loops before publishing bulk imports.

## Verification

- Run `vendor/bin/pest packages/redirects/tests` when package tests exist.
- Run the relevant host-app migration or package install flow in a disposable database.
- Open the relevant frontend surface and compare it with the screenshot plan.

## Package Manifest

- Composer name: `capell-app/redirects`
- Product group: Capell Foundation
- Kind: package
- Tier: free
- Bundle: foundation
- Contexts: `frontend`
- Requires: `capell-app/core`
- Optional dependencies: None listed.

## Commands

- None proven in this package directory.

## Routes And Config

- Config: packages/redirects/config/redirects.php

## Migrations

- Migration: create_redirect_health_snapshots_table.php

## ERD Excerpt

This package has no committed ERD excerpt. Use implementation notes and extension points instead of inventing schema.

## Screenshot Automation

Deployment should read [screenshots.json](screenshots.json), install the package with demo data, resolve each frontend URL, and write images to `public/docs/screenshots/packages/redirects`.

- Redirect health snapshot output.
