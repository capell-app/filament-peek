# Theme Studio Core

Status: **Available, settings-owning** · Kind: **package** · Tier: **premium** · Bundle: **theme-studio** · Contexts: **frontend, console** · Product group: **Capell Theme Studio**

This page is the consolidated implementation overview for the Theme Studio Core package. It is extracted from the package README, service providers, migrations, config files, routes, resources, models, actions, and the shared Capell ERD notes where available.

## What This Plugin Adds

Theme Studio Core provides the contracts, registry, runtime data, preview context, token rendering, and Blade rendering support used by Theme Studio renderers.

- Theme registry and runtime resolver.
- Theme page adapters and renderer contracts.
- Preview context and signed preview support.
- Theme token store and renderer.
- Data objects for brand profiles, navigation, hero, content, proof, CTA, feature, footer, and theme pages.

## Developer Notes

Defines the runtime boundary for theme packages so renderer packages can register sections and presets without owning content schema.

- ThemeStudioCoreServiceProvider registers core services.
- Settings migration creates theme studio settings.
- Actions render current theme pages and resolve brand profile/runtime.
- Middleware resolves theme preview context.
- Contracts cover section rendering, page adapters, runtime settings, and theme renderers.

## Operational Notes

Makes theme previews and renderer selection consistent across the package-based CMS foundation.

- Adds Theme Studio settings migration.
- Adds preview context middleware support.
- Registers theme runtime services.
- No admin navigation by itself.
- No public route file is present.

## Data And Retention

- This package owns Theme Studio settings.
- It does not create content tables.
- Runtime data is composed from settings, theme definitions, core page data, and renderer packages.

## Screenshot Plan

- Theme preview URL output.
- Frontend page rendered through Theme Studio Core.
- Brand profile settings.
- Renderer selection proof.

## Pitfalls

- Renderer packages must register with the ThemeRegistry.
- Signed preview context must be kept scoped and temporary.
- Run settings migrations before opening admin surfaces that expect Theme Studio settings.

## Verification

- Run `vendor/bin/pest packages/theme-studio-core/tests` when package tests exist.
- Run the relevant host-app migration or package install flow in a disposable database.
- Open the listed admin or frontend surface and compare it with the screenshot plan.

## Package Manifest

- Composer name: `capell-app/theme-studio-core`
- Product group: Capell Theme Studio
- Kind: package
- Tier: premium
- Bundle: theme-studio
- Contexts: `frontend`, `console`
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

- Settings migration: create_theme_studio_settings.php

## ERD Excerpt

This package has no committed ERD excerpt. Use implementation notes and extension points instead of inventing schema.

## Screenshot Automation

Deployment should read [screenshots.json](screenshots.json), install the package with demo data, resolve each admin surface or frontend URL, and write images to `public/docs/screenshots/packages/theme-studio-core`.

- Theme preview URL output.
- Frontend page rendered through Theme Studio Core.
- Brand profile settings.
- Renderer selection proof.
