# Default Theme

Status: **Available, no schema impact except settings** · Kind: **theme** · Tier: **free** · Bundle: **foundation** · Contexts: **frontend, admin** · Product group: **Capell Foundation**

## What This Plugin Adds

Default Theme ships Capell frontend theme infrastructure, Tailwind asset generation, Blade directives, media URL handling, and theme settings.

- Default theme service provider.
- Tailwind asset generation command.
- Theme settings schema and settings migration.
- SVG media component and Capell URL generator.
- Blade directives for frontend rendering.

## Why It Matters

**For developers:** Provides the baseline Laravel view and asset pipeline that other themes and frontend packages can target.

**For teams:** Gives each Capell installation a standard frontend foundation before a custom or Theme Studio renderer is added.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Default theme settings screen.
- Frontend page using the default theme.
- Generated Tailwind asset output review.

## Technical Shape

- DefaultThemeServiceProvider and AdminServiceProvider register theme services and settings.
- Config file: capell-default-theme.php.
- Settings migration creates default theme settings.
- GenerateTailwindAssetsCommand writes frontend CSS assets.
- BladeDirectives and CapellUrlGenerator support rendering.

## Data Model

- This package does not create content tables.
- It owns settings through create_default_theme_settings.php.
- Theme output depends on core site, page, layout, and media data.

## Install Impact

- Adds default theme settings.
- Adds Tailwind asset generation command.
- Adds config keys for asset build tool, npm dependencies, Tailwind sources, and media URL behaviour.
- No public routes are registered by this package.

## Commands

- `capell:frontend-tailwind-assets {--report : Print the aggregated assets report instead of writing files} {--output-path= : Base absolute path for generated CSS files; theme key is appended per enabled Theme (e.g. frontend-default.css)} {--theme-key= : Only regenerate the CSS file for the Theme with this key}` (packages/default-theme/src/Console/Commands/GenerateTailwindAssetsCommand.php)

## Admin And Access

- None proven in this package directory.

- None proven in this package directory.

## Common Pitfalls

- Regenerate assets after changing theme colours or source paths.
- Match asset_build_tool to the host app.
- Set media URL config before production media rendering.

## Quick Start

1. Install the package with `composer require capell-app/default-theme`.
2. Run the package migrations or the Capell package installer required by the host app.
3. Open the new admin or frontend surface and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../theme-studio-core/README.md](../theme-studio-core/README.md)
- [../theme-agency/README.md](../theme-agency/README.md)
