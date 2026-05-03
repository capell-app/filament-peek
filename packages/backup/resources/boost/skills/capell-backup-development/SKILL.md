---
name: capell-backup-development
description: Use when editing Capell Backup exports, imports, restores, or package readers.
---

# Capell Backup

Export, import, restore, WordPress import, dependency graph, and validation workflows.

## Look

- `packages/backup/src`
- `packages/backup/docs`
- `packages/backup/README.md`

## Rules

- Validate imports before writes; prefer previewable restore steps.
- Keep package readers/writers isolated from Filament pages.
- Preserve relation resolution and dependency ordering.
- Run `vendor/bin/pest packages/backup/tests`.
