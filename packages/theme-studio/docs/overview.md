# Theme Studio

Status: **Available, metapackage** · Kind: **bundle** · Tier: **premium** · Bundle: **theme-studio** · Contexts: **frontend, admin** · Product group: **Capell Theme Studio**

This page is the consolidated implementation overview for the Theme Studio package. It is extracted from the package README, service providers, migrations, config files, routes, resources, models, actions, and the shared Capell ERD notes where available.

## What This Plugin Adds

Theme Studio is the commercial theme system bundle that installs Theme Studio Core, Theme Studio Admin, and the Agency, Corporate, and SaaS renderers.

- Metapackage dependency bundle.
- Theme Studio Core runtime.
- Theme Studio Admin Filament page.
- Agency, Corporate, and SaaS renderer packages.

## Developer Notes

Provides one Composer install target for the full Theme Studio package set without adding runtime code in this directory.

- composer.json type is metapackage.
- Requires theme-agency, theme-corporate, theme-saas, theme-studio-admin, and theme-studio-core.
- No src, config, routes, migrations, resources, or tests are present in this directory.

## Operational Notes

Installs the full theme workflow in one package instead of selecting each renderer and admin package manually.

- Installs the Theme Studio package set.
- No direct database changes from this directory.
- Admin and settings impact come from bundled packages.
- No public routes are registered by this package directly.

## Data And Retention

- This metapackage does not own data.
- Schema impact comes from Theme Studio Core settings and Theme Studio Admin/Core dependencies.

## Screenshot Plan

- Theme Studio admin page.
- Theme preset selection.
- Theme preview URL workflow.
- Frontend output from each bundled renderer.

## Pitfalls

- Do not document this as a runtime provider; it is a dependency bundle.
- Run setup for the installed child packages where required.

## Verification

- Run `vendor/bin/pest packages/theme-studio/tests` when package tests exist.
- Run the relevant host-app migration or package install flow in a disposable database.
- Open the listed admin or frontend surface and compare it with the screenshot plan.

## Package Manifest

- Composer name: `capell-app/theme-studio`
- Product group: Capell Theme Studio
- Kind: bundle
- Tier: premium
- Bundle: theme-studio
- Contexts: `frontend`, `admin`
- Requires: `capell-app/theme-studio-core`, `capell-app/theme-studio-admin`, `capell-app/theme-corporate`, `capell-app/theme-agency`, `capell-app/theme-saas`
- Optional dependencies: None listed.

## Admin Surfaces

- None proven in this package directory.

## Commands

- None proven in this package directory.

## Routes And Config

- None proven in this package directory.

## Permissions And Gates

- None proven in this package directory.

## Migrations

- None proven in this package directory.

## ERD Excerpt

This package has no committed ERD excerpt. Use implementation notes and extension points instead of inventing schema.

## Screenshot Automation

Deployment should read [screenshots.json](screenshots.json), install the package with demo data, resolve each admin surface or frontend URL, and write images to `public/docs/screenshots/packages/theme-studio`.

- Theme Studio admin page.
- Theme preset selection.
- Theme preview URL workflow.
- Frontend output from each bundled renderer.
