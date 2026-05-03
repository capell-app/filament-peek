---
name: capell-deployments-development
description: Use when editing Capell Deployments repository connections or Composer publishing.
---

# Capell Deployments

Repository deployment connections and Composer requirement publishing through pull requests.

## Look

- `packages/deployments/src`
- `packages/deployments/docs`
- `packages/deployments/README.md`

## Rules

- Treat repository writes as previewable, auditable operations.
- Keep OAuth/controllers thin; publishing decisions belong in Actions/services.
- Never bypass Composer constraint validation before PR creation.
- Run `vendor/bin/pest packages/deployments/tests`.
