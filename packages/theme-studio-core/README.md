# Theme Studio Core

Status: **Available, settings-owning** · Kind: **package** · Tier: **premium** · Bundle: **theme-studio** · Contexts: **frontend, console** · Product group: **Capell Theme Studio**

## What This Plugin Adds

Theme Studio Core provides the contracts, registry, runtime data, preview context, token rendering, and Blade rendering support used by Theme Studio renderers.

- Theme registry and runtime resolver.
- Theme page adapters and renderer contracts.
- Preview context and signed preview support.
- Theme token store and renderer.
- Data objects for brand profiles, navigation, hero, content, proof, CTA, feature, footer, and theme pages.

## Why It Matters

**For developers:** Defines the runtime boundary for theme packages so renderer packages can register sections and presets without owning content schema.

**For teams:** Makes theme previews and renderer selection consistent across the package-based CMS foundation.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Theme preview URL output.
- Frontend page rendered through Theme Studio Core.
- Brand profile settings.
- Renderer selection proof.

## Technical Shape

- ThemeStudioCoreServiceProvider registers core services.
- Settings migration creates theme studio settings.
- Actions render current theme pages and resolve brand profile/runtime.
- Middleware resolves theme preview context.
- Contracts cover section rendering, page adapters, runtime settings, and theme renderers.

## Data Model

- This package owns Theme Studio settings.
- It does not create content tables.
- Runtime data is composed from settings, theme definitions, core page data, and renderer packages.

## Install Impact

- Adds Theme Studio settings migration.
- Adds preview context middleware support.
- Registers theme runtime services.
- No admin navigation by itself.
- No public route file is present.

## Commands

- None proven in this package directory.

## Admin And Access

- None proven in this package directory.

- None proven in this package directory.

## Common Pitfalls

- Renderer packages must register with the ThemeRegistry.
- Signed preview context must be kept scoped and temporary.
- Run settings migrations before opening admin surfaces that expect Theme Studio settings.

## Quick Start

1. Install the package with `composer require capell-app/theme-studio-core`.
2. Run the package migrations or the Capell package installer required by the host app.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../theme-studio-admin/README.md](../theme-studio-admin/README.md)
- [../theme-agency/README.md](../theme-agency/README.md)
- [../theme-corporate/README.md](../theme-corporate/README.md)
- [../theme-saas/README.md](../theme-saas/README.md)
