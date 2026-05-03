---
name: workspaces
description: Use when editing Capell Workspaces drafts, approvals, previews, scheduling, or publishing.
---

# Capell Workspaces

Draft workspaces, approvals, preview links, scheduling, version history, rollback, and controlled publishing.

## Look

- `packages/workspaces/src`
- `packages/workspaces/docs`
- `packages/workspaces/README.md`

## Rules

- Draftable models must use registered morph maps and existing replication actions.
- Publishing, rollback, approval, and schedule changes belong in Actions.
- Preserve preview-link security and workspace isolation.
- Run `vendor/bin/pest packages/workspaces/tests`.
