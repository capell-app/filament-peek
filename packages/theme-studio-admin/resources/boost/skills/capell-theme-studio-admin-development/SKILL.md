---
name: capell-theme-studio-admin-development
description: Use when editing Theme Studio admin staging, preview, approval, or publishing.
---

# Capell Theme Studio Admin

Filament admin workflow for staging, reviewing, previewing, approving, and publishing theme drafts.

## Look

- `packages/theme-studio-admin/src`
- `packages/theme-studio-admin/docs`
- `packages/theme-studio-admin/README.md`

## Rules

- Keep readiness checks and publishing decisions in Actions.
- Preserve preview, approval, and workspace draft boundaries.
- Settings schema changes must remain migration-safe.
- Run `vendor/bin/pest packages/theme-studio-admin/tests`.
