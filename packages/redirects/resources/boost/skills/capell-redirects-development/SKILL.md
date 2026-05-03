---
name: capell-redirects-development
description: Use when editing Capell Redirects, changed URL capture, import/export, or resolving.
---

# Capell Redirects

Admin redirect management, automatic changed-page redirects, import/export, and health snapshots.

## Look

- `packages/redirects/src`
- `packages/redirects/docs`
- `packages/redirects/README.md`

## Rules

- Avoid redirect loops and ambiguous source matching.
- Automatic redirects from page URL changes belong in listeners/actions.
- Import/export must validate destinations before writing.
- Run `vendor/bin/pest packages/redirects/tests`.
