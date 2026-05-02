# Theme Default

Status: **Available, no schema impact** · Kind: **theme** · Tier: **free** · Bundle: **foundation** · Contexts: **admin, frontend** · Product group: **Capell Foundation**

This page is the consolidated implementation overview for the Theme Default package. It is extracted from the package README, service providers, migrations, config files, routes, resources, models, actions, and the shared Capell ERD notes where available.

## What This Plugin Adds

Theme Default is the legacy package path for the default Capell theme provider and frontend interceptor.

- DefaultThemeServiceProvider under the theme-default directory.
- Default theme interceptor.
- Blade directive support.

## Developer Notes

Keeps compatibility for installations that reference the theme-default package directory while default theme functionality lives under the capell-app/default-theme composer package.

- DefaultThemeServiceProvider registers theme-default services.
- DefaultThemeInterceptor handles theme interception.
- No migrations, config, routes, resources, or models are present in this directory.

## Operational Notes

Keeps existing default theme installations working during package naming cleanup.

- Adds default theme provider compatibility.
- No database changes.
- No admin navigation by itself.
- No public routes by itself.

## Data And Retention

- This package does not own data.
- It depends on core site/page/layout records and default theme rendering services.

## Screenshot Plan

- Frontend page rendered through the default theme.
- Theme selection showing the default theme.

## Pitfalls

- Prefer the canonical default-theme package docs for new work.
- Check composer package naming because both directories point at capell-app/default-theme in this repo.

## Verification

- Run `vendor/bin/pest packages/theme-default/tests` when package tests exist.
- Run the relevant host-app migration or package install flow in a disposable database.
- Open the listed admin or frontend surface and compare it with the screenshot plan.

## Package Manifest

- Composer name: `capell-app/default-theme`
- Product group: Capell Foundation
- Kind: theme
- Tier: free
- Bundle: foundation
- Contexts: `admin`, `frontend`
- Requires: Not declared.
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

Deployment should read [screenshots.json](screenshots.json), install the package with demo data, resolve each admin surface or frontend URL, and write images to `public/docs/screenshots/packages/theme-default`.

- Frontend page rendered through the default theme.
- Theme selection showing the default theme.
