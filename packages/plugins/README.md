# Plugins

Status: **Pipeline, no Composer manifest in this directory**

## What This Plugin Adds

Plugins is a config-only package directory for the Capell plugin and marketplace install intent work.

- Configures the package marketplace feature flag.
- Defines Anystack API and Composer repository settings.
- Defines license heartbeat cache and offline grace settings.
- Defines Composer binary and timeout settings for install operations.

## Why It Matters

**For developers:** This keeps marketplace and install intent settings separate from core Capell while the runtime package shape is still being finalised.

**For teams:** It describes the operational settings needed before package install intent and license checks can become a supported site operation.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Package marketplace index.
- Package detail or install intent screen.
- License verification state.
- Install progress or result state.

## Technical Shape

- Config file: capell-plugins.php.
- No Composer manifest is present in this directory.
- No service provider is present in this directory.
- No migrations, models, routes, resources, actions, jobs, or tests are present in this directory.
- The shared ERD documents plugin extensions, licenses, install intents, marketplace instances, installed receipts, and plugin migrations as the intended data model.

## Data Model

- This directory does not own database migrations.
- The shared ERD includes plugin_extensions, plugin_licenses, plugin_license_activations, capell_plugin_install_intents, capell_plugin_marketplace_instances, installed_receipts, deployment_connections, and capell_plugin_migrations.

## Install Impact

- Adds config keys only.
- Does not add admin navigation by itself.
- Does not add permissions by itself.
- Does not add public routes by itself.
- Does not add database tables by itself.

## Commands

- None proven in this package directory.

## Admin And Access

- None proven in this package directory.

- None proven in this package directory.

## Common Pitfalls

- Do not document this directory as an installable package until it has a Composer manifest.
- Keep CAPELL_PLUGINS_ENABLED false until the runtime package and migrations are confirmed.
- Verify Anystack endpoint, Composer binary, and license heartbeat settings before enabling install intent workflows.

## Quick Start

1. Review config in `packages/plugins/config/capell-plugins.php`.
2. Confirm the runtime marketplace package and migrations in the host app.
3. Open the marketplace/admin surface only after the feature flag and backing package are verified.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../mcp/README.md](../mcp/README.md)
- [../developer-tools/README.md](../developer-tools/README.md)
