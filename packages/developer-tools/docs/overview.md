# Developer Tools

Status: **Available, no schema impact** · Kind: **package** · Tier: **premium** · Bundle: **operations** · Contexts: **admin, console** · Product group: **Capell Operations**

This page is the consolidated implementation overview for the Developer Tools package. It is extracted from the package README, service providers, migrations, config files, routes, resources, models, actions, and the shared Capell ERD notes where available.

## What This Plugin Adds

Developer Tools adds operational diagnostics for cache, configuration drift, migrations, packages, registries, queues, permissions, setup health, and Tailwind build status.

- System health admin pages.
- Developer tools dashboard page.
- Permission audit report.
- Queue health report.
- Health widgets for cache, content, migrations, registry, setup, packages, and Tailwind.

## Developer Notes

Keeps diagnostics in actions and data objects so admin pages can show health information without hard-coded checks in the UI.

- DeveloperToolsServiceProvider and AdminServiceProvider register admin pages and widgets.
- Actions build each health report.
- Data objects describe report rows and dashboard state.
- FailedJob model supports queue reporting.
- No package migrations are present.

## Operational Notes

Helps operators and agencies see setup problems before they become publishing or deployment issues.

- Adds admin pages for developer diagnostics.
- Adds dashboard widgets.
- No database changes.
- No public routes are registered by this package.

## Data And Retention

- This package does not own schema.
- It reads existing Laravel and Capell state such as config, migrations, failed jobs, permissions, packages, registries, and Tailwind outputs.

## Screenshot Plan

- Developer tools dashboard.
- System health page.
- Permission audit page.
- Queue health page.
- Health widgets on the admin dashboard.

## Pitfalls

- Some checks depend on host-app conventions and may need configuration.
- Queue health needs access to failed job data.
- Permission audit output is only useful when permissions are registered.

## Verification

- Run `vendor/bin/pest packages/developer-tools/tests` when package tests exist.
- Run the relevant host-app migration or package install flow in a disposable database.
- Open the listed admin or frontend surface and compare it with the screenshot plan.

## Package Manifest

- Composer name: `capell-app/developer-tools`
- Product group: Capell Operations
- Kind: package
- Tier: premium
- Bundle: operations
- Contexts: `admin`, `console`
- Requires: `capell-app/core`, `capell-app/admin`
- Optional dependencies: None listed.

## Admin Surfaces

- DeveloperToolsPage (packages/developer-tools/src/Filament/Pages/DeveloperToolsPage.php, slug `developer-tools`)
- PermissionAuditPage (packages/developer-tools/src/Filament/Pages/PermissionAuditPage.php, slug `reports/permission-audit`)
- QueueHealthPage (packages/developer-tools/src/Filament/Pages/QueueHealthPage.php, slug `reports/queue-health`)
- SystemHealthPage (packages/developer-tools/src/Filament/Pages/SystemHealthPage.php, slug `system-health`)

## Commands

- None proven in this package directory.

## Routes And Config

- None proven in this package directory.

## Permissions And Gates

- Gate: CacheHealthWidgetAbstract: `admin`, `super_admin`
- Gate: ConfigDriftWidgetAbstract: `super_admin`
- Gate: ContentHealthWidgetAbstract: `editor`, `admin`, `super_admin`
- Gate: DeveloperToolsPage: Gate `accessDeveloperTools`, `viewDeveloperTools`
- Gate: MigrationsHealthWidgetAbstract: `super_admin`
- Gate: PackagesInstalledWidgetAbstract: `super_admin`
- Gate: QueueHealthPage: Gate `accessDeveloperTools`, `viewDeveloperTools`
- Gate: RegistryHealthWidgetAbstract: `super_admin`
- Gate: SetupHealthWidgetAbstract: settings-gated only
- Gate: SiteHealthWidgetAbstract: settings-gated only
- Gate: TailwindBuildStatusWidgetAbstract: `super_admin`

## Migrations

- None proven in this package directory.

## ERD Excerpt

This package has no committed ERD excerpt. Use implementation notes and extension points instead of inventing schema.

## Screenshot Automation

Deployment should read [screenshots.json](screenshots.json), install the package with demo data, resolve each admin surface or frontend URL, and write images to `public/docs/screenshots/packages/developer-tools`.

- Developer tools dashboard.
- System health page.
- Permission audit page.
- Queue health page.
- Health widgets on the admin dashboard.
