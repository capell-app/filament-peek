---
name: capell-seo-tools-development
description: Use when editing Capell SEO metadata, sitemaps, schema, audits, or AI briefs.
---

# Capell SEO Tools

Metadata panels, sitemaps, structured data, broken links, Search Console insights, and publish checks.

## Look

- `packages/seo-tools/src`
- `packages/seo-tools/docs`
- `packages/seo-tools/README.md`

## Rules

- Keep publish gates explainable and non-destructive.
- Sitemaps and schema must respect site/language scope.
- AI brief and metadata suggestions should be previewed before applying.
- Run `vendor/bin/pest packages/seo-tools/tests`.
