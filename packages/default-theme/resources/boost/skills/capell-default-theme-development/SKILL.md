---
name: capell-default-theme-development
description: Use when editing Capell Default Theme Blade, Tailwind assets, media URLs, or settings.
---

# Capell Default Theme

Default frontend theme infrastructure: Blade components, Tailwind assets, URL helpers, and theme settings.

## Look

- `packages/default-theme/src`
- `packages/default-theme/resources`
- `packages/default-theme/README.md`

## Rules

- Keep components generic; branded renderers belong in Theme Studio packages.
- Preserve safe output rules for Blade and SVG media.
- Theme settings must remain optional and migration-safe.
- Run `vendor/bin/pest packages/default-theme/tests`.
