# Authentication Log

Status: **Available, schema-owning** · Kind: **package** · Tier: **premium** · Bundle: **operations** · Contexts: **admin** · Product group: **Capell Operations**

This page is the consolidated implementation overview for the Authentication Log package. It is extracted from the package README, service providers, migrations, config files, routes, resources, models, actions, and the shared Capell ERD notes where available.

## What This Plugin Adds

Authentication Log records login, failed login, logout, and admin/user activity metadata for Capell users.

- Filament resource for authentication logs.
- Dashboard widget for recent authentication activity.
- Settings schema for authentication log behaviour.
- Middleware for admin and user activity tracking.

## Developer Notes

Wraps Rappasoft Laravel Authentication Log with Capell settings, resources, widgets, query actions, and IP resolution policy.

- AuthenticationLogServiceProvider and AdminServiceProvider register the package.
- Config file: authentication-log.php.
- Migration creates authentication_log.
- Model: AuthenticationLog.
- Filament resource: AuthenticationLogResource.
- Middleware: AdminActivityMiddleware and UserActivityMiddleware.

## Operational Notes

Helps site operators review access activity and spot account behaviour that needs follow-up.

- Adds authentication_log table.
- Adds settings migration.
- Adds authentication log admin resource and widget.
- Listens to Laravel auth events configured in authentication-log.php.
- May send new-device or failed-login notifications depending on config.

## Data And Retention

- authentication_log stores authenticatable type/id, IP address, user agent, login time, and logout time.
- Records belong polymorphically to authenticatable users.
- Config purge value defaults to 365 days.

## Screenshot Plan

- Authentication logs admin index.
- Authentication log table filters.
- Dashboard widget.
- Authentication log settings screen.

## Pitfalls

- Set CDN IP header config before trusting IP addresses behind a proxy.
- Confirm notification settings before production rollout.
- Run migrations before loading the resource.

## Verification

- Run `vendor/bin/pest packages/authentication-log/tests` when package tests exist.
- Run the relevant host-app migration or package install flow in a disposable database.
- Open the listed admin or frontend surface and compare it with the screenshot plan.

## Package Manifest

- Composer name: `capell-app/authentication-log`
- Product group: Capell Operations
- Kind: package
- Tier: premium
- Bundle: operations
- Contexts: `admin`
- Requires: `capell-app/admin`
- Optional dependencies: None listed.

## Admin Surfaces

- AuthenticationLogResource (packages/authentication-log/src/Filament/Resources/AuthenticationLogs/AuthenticationLogResource.php)

## Commands

- None proven in this package directory.

## Routes And Config

- Config: packages/authentication-log/config/authentication-log.php

## Permissions And Gates

- Gate: AuthenticationLogsWidget: `admin`, `super_admin`

## Migrations

- Migration: create_authentication_log_table.php
- Settings migration: add_authentication_log_settings.php

## ERD Excerpt

```mermaid
erDiagram
    USERS ||--o{ AUTHENTICATION_LOG : records

    AUTHENTICATION_LOG {
        bigint id PK
        string authenticatable_type
        bigint authenticatable_id
        string ip_address
        text user_agent
        timestamp login_at
        timestamp logout_at
    }
```

## Screenshot Automation

Deployment should read [screenshots.json](screenshots.json), install the package with demo data, resolve each admin surface or frontend URL, and write images to `public/docs/screenshots/packages/authentication-log`.

- Authentication logs admin index.
- Authentication log table filters.
- Dashboard widget.
- Authentication log settings screen.
