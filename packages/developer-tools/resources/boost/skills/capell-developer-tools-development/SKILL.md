---
name: capell-developer-tools-development
description: Use when editing Capell Developer Tools diagnostics, health checks, or reports.
---

# Capell Developer Tools

Operational diagnostics for cache, config, migrations, packages, queues, permissions, and setup health.

## Look

- `packages/developer-tools/src`
- `packages/developer-tools/docs`
- `packages/developer-tools/README.md`

## Rules

- Diagnostics should observe and report; avoid hidden mutations.
- Keep health builders in Actions/Data for easy testing.
- Permission and setup reports must be explicit about risk level.
- Run `vendor/bin/pest packages/developer-tools/tests`.
