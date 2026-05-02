# Authentication Log

Status: **Available, schema-owning** · Kind: **package** · Tier: **premium** · Bundle: **operations** · Contexts: **admin** · Product group: **Capell Operations**

## What This Plugin Adds

Authentication Log records login, failed login, logout, and admin/user activity metadata for Capell users.

- Filament resource for authentication logs.
- Dashboard widget for recent authentication activity.
- Settings schema for authentication log behaviour.
- Middleware for admin and user activity tracking.

## Why It Matters

**For developers:** Wraps Rappasoft Laravel Authentication Log with Capell settings, resources, widgets, query actions, and IP resolution policy.

**For teams:** Helps site operators review access activity and spot account behaviour that needs follow-up.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Authentication logs admin index.
- Authentication log table filters.
- Dashboard widget.
- Authentication log settings screen.

## Technical Shape

- AuthenticationLogServiceProvider and AdminServiceProvider register the package.
- Config file: authentication-log.php.
- Migration creates authentication_log.
- Model: AuthenticationLog.
- Filament resource: AuthenticationLogResource.
- Middleware: AdminActivityMiddleware and UserActivityMiddleware.

## Data Model

- authentication_log stores authenticatable type/id, IP address, user agent, login time, and logout time.
- Records belong polymorphically to authenticatable users.
- Config purge value defaults to 365 days.

## Install Impact

- Adds authentication_log table.
- Adds settings migration.
- Adds authentication log admin resource and widget.
- Listens to Laravel auth events configured in authentication-log.php.
- May send new-device or failed-login notifications depending on config.

## Commands

- None proven in this package directory.

## Admin And Access

- AuthenticationLogResource (packages/authentication-log/src/Filament/Resources/AuthenticationLogs/AuthenticationLogResource.php)

- Gate: AuthenticationLogsWidget: `admin`, `super_admin`

## Common Pitfalls

- Set CDN IP header config before trusting IP addresses behind a proxy.
- Confirm notification settings before production rollout.
- Run migrations before loading the resource.

## Quick Start

1. Install the package with `composer require capell-app/authentication-log`.
2. Run the package migrations or the Capell package installer required by the host app.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../developer-tools/README.md](../developer-tools/README.md)
