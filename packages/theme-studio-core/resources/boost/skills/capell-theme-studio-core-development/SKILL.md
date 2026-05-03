---
name: capell-theme-studio-core-development
description: Use when editing Theme Studio contracts, registry, preview context, tokens, or rendering.
---

# Capell Theme Studio Core

Theme registry, runtime data, preview context, token rendering, and renderer contracts.

## Look

- `packages/theme-studio-core/src`
- `packages/theme-studio-core/docs`
- `packages/theme-studio-core/README.md`

## Rules

- Keep renderers behind core contracts.
- Preview context and signed preview support must stay isolated.
- Token/rendering changes affect all renderer packages.
- Run `vendor/bin/pest packages/theme-studio-core/tests`.
