# Filament Peek

Status: **Available, no schema impact** · Kind: **package** · Tier: **premium** · Bundle: **publishing-pro** · Contexts: **admin, frontend** · Product group: **Capell Publishing Pro**

## What This Plugin Adds

Filament Peek adds optional iframe preview actions for Capell admin and Workspaces draft review.

- Admin panel extender for Filament Peek.
- Workspace preview action contributor.
- Peek preview action for workspaces.

## Why It Matters

**For developers:** Integrates preview actions through admin extenders and Workspaces contributors instead of changing core resources directly.

**For teams:** Lets editors preview draft content from the admin workflow before publishing or approving it.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Page or workspace edit screen with preview action.
- Peek iframe preview panel.
- Workspace draft review screen with preview action.

## Technical Shape

- FilamentPeekServiceProvider and AdminServiceProvider register the package.
- FilamentPeekAdminPanelExtender connects into the admin panel.
- WorkspacePeekPreviewActionContributor contributes preview actions when Workspaces is present.
- No migrations, config, or routes are present in this package.

## Data Model

- This package does not own data.
- It depends on existing page, workspace, and preview URL state supplied by Capell and Workspaces.

## Install Impact

- Adds preview action integration to the admin surface.
- No database changes.
- No public routes in this package.
- Requires the host app to include the relevant Filament Peek dependency/configuration.

## Commands

- None proven in this package directory.

## Admin And Access

- None proven in this package directory.

- None proven in this package directory.

## Common Pitfalls

- Install Workspaces before expecting workspace-specific preview actions.
- Iframe preview must be allowed by the rendered frontend response.

## Quick Start

1. Install the package with `composer require capell-app/filament-peek`.
2. Register the package provider through Composer discovery and clear cached config if the host app uses config caching.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../workspaces/README.md](../workspaces/README.md)
