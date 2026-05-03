---
name: capell-campaigns-development
description: Use when editing Capell Campaigns landing pages, CTAs, goals, or attribution.
---

# Capell Campaigns

Campaign groups, landing pages, CTA blocks, conversion goals, UTM attribution, and reporting.

## Look

- `packages/campaigns/src`
- `packages/campaigns/docs`
- `packages/campaigns/README.md`

## Rules

- Keep attribution and conversion writes explicit and testable.
- Mosaic configurators should not own campaign domain logic.
- Preserve page schema extender behaviour for campaign fields.
- Run `vendor/bin/pest packages/campaigns/tests`.
