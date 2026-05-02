# Theme Default

Status: **Available, no schema impact** · Kind: **theme** · Tier: **free** · Bundle: **foundation** · Contexts: **admin, frontend** · Product group: **Capell Foundation**

## What This Plugin Adds

Theme Default is the legacy package path for the default Capell theme provider and frontend interceptor.

- DefaultThemeServiceProvider under the theme-default directory.
- Default theme interceptor.
- Blade directive support.

## Why It Matters

**For developers:** Keeps compatibility for installations that reference the theme-default package directory while default theme functionality lives under the capell-app/default-theme composer package.

**For teams:** Keeps existing default theme installations working during package naming cleanup.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Frontend page rendered through the default theme.
- Theme selection showing the default theme.

## Technical Shape

- DefaultThemeServiceProvider registers theme-default services.
- DefaultThemeInterceptor handles theme interception.
- No migrations, config, routes, resources, or models are present in this directory.

## Data Model

- This package does not own data.
- It depends on core site/page/layout records and default theme rendering services.

## Install Impact

- Adds default theme provider compatibility.
- No database changes.
- No admin navigation by itself.
- No public routes by itself.

## Commands

- None proven in this package directory.

## Admin And Access

- None proven in this package directory.

- None proven in this package directory.

## Common Pitfalls

- Prefer the canonical default-theme package docs for new work.
- Check composer package naming because both directories point at capell-app/default-theme in this repo.

## Quick Start

1. Install the package with `composer require capell-app/default-theme`.
2. Register the package provider through Composer discovery and clear cached config if the host app uses config caching.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../default-theme/README.md](../default-theme/README.md)
- [../theme-studio-core/README.md](../theme-studio-core/README.md)
