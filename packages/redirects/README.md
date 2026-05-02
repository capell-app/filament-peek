# Redirects

Status: **Available, schema-owning** · Kind: **package** · Tier: **free** · Bundle: **foundation** · Contexts: **admin, frontend** · Product group: **Capell Foundation**

## What This Plugin Adds

Redirects adds admin redirect management, automatic redirect creation from changed page URLs, import/export support, and redirect health snapshots.

- Redirect Filament resource.
- Redirect importer and exporter.
- Automatic redirect creation action.
- Page URL redirect recorder and resolver support.
- Redirect health snapshot actions and model.

## Why It Matters

**For developers:** Provides resolver and recorder contracts so admin and frontend code can create and resolve redirects without coupling to one implementation.

**For teams:** Helps site operators preserve traffic and search value when URLs change.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Redirects admin index.
- Create/edit redirect form.
- Redirect import workflow.
- Redirect export workflow.
- Redirect health snapshot output.

## Technical Shape

- RedirectsServiceProvider registers the package.
- Config file: redirects.php.
- Migration creates redirect_health_snapshots.
- Filament resource: RedirectResource.
- Importer/exporter handle bulk redirect data.
- Listener creates redirects for changed page URLs.

## Data Model

- redirect_health_snapshots stores redirect health results.
- Redirect records appear to integrate with core page URL redirect behaviour rather than a package-owned redirects migration in this package.
- Deletion and retention for health snapshots should be verified against site operations policy.

## Install Impact

- Adds redirect admin resource.
- Adds redirect_health_snapshots table.
- Adds config for automatic redirects and status code.
- No package route file is present.
- Can create redirects when page URLs change.

## Commands

- None proven in this package directory.

## Admin And Access

- ManageRedirects (packages/redirects/src/Filament/Resources/Redirects/Pages/ManageRedirects.php)
- RedirectResource (packages/redirects/src/Filament/Resources/Redirects/RedirectResource.php)

- Policy: RedirectPolicy (packages/redirects/src/Policies/RedirectPolicy.php)
- Gate: ManageRedirects: Gate `import`, `export`

## Common Pitfalls

- Confirm where redirect records are stored in the host app before importing.
- Keep automatic redirects enabled only when changed page URLs should produce 301s.
- Validate redirect loops before publishing bulk imports.

## Quick Start

1. Install the package with `composer require capell-app/redirects`.
2. Run the package migrations or the Capell package installer required by the host app.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../seo-tools/README.md](../seo-tools/README.md)
- [../navigation/README.md](../navigation/README.md)
