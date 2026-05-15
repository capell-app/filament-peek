---
title: 'Dashboard Reports Overview'
description: 'How the Capell Dashboard Reports package adds reusable admin dashboard health and publishing trend widgets.'
---

# Dashboard Reports Overview

Dashboard Reports adds reusable admin dashboard widgets for Capell sites that need editorial health and publishing activity surfaced in one place.

Use it when a project needs a package-owned reporting layer, but does not need a bespoke analytics package. The package stays admin-only and reads existing Capell page state instead of creating new content records.

## What It Adds

- Content health reporting for scheduled pages, expired pages, pages without URLs, and stale published pages.
- Publishing trend reporting across common date windows.
- Dashboard widgets registered into the main Capell admin dashboard.
- A `ContentHealthDataProvider` implementation that can replace the admin package's null provider when the package is installed.
- Dashboard settings contribution for report visibility.

## Admin Surface

Dashboard Reports registers these widgets through `CapellAdmin::registerDashboardWidget(...)`:

| Widget                       | Purpose                                                |
| ---------------------------- | ------------------------------------------------------ |
| `ContentHealthWidget`        | Shows content issues that need editorial attention.    |
| `PublishingTrendChartWidget` | Shows published and scheduled page activity over time. |

`ContentHealthWidget` is only visible when the resolved content health provider returns at least one issue. Its data is computed and cached by Livewire for 300 seconds.

## Data Sources

The package reads Capell core `Page` records through `SiteScope::applyForCurrentActor(...)`, so editors only see counts for sites they can access.

| Report             | Source                                                                      |
| ------------------ | --------------------------------------------------------------------------- |
| Scheduled pages    | `Page::pending()`                                                           |
| Expired pages      | `Page::expired()`                                                           |
| Pages without URLs | Pages without related page URL records                                      |
| Stale pages        | Published pages older than the configured stale-day threshold               |
| Publishing trend   | Published and scheduled page counts bucketed across the selected date range |

Dashboard Reports does not create reporting tables. It computes the current dashboard state from the installed site's page records.

## Extension Notes

If a project needs different content health rules, bind `Capell\Admin\Contracts\Dashboard\ContentHealthDataProvider` before the Dashboard Reports admin provider boots. The package only replaces the admin null provider; it will not override a real provider already registered by the host app or another package.

Put new report calculations in `src/Actions/Dashboard/` and keep Filament widgets thin. Tests should target the Action output first, then widget visibility where needed.

## Install And Verify

Install the package in a host Capell app:

```bash
composer require capell-app/dashboard-reports
```

Then verify the package in this repository with:

```bash
vendor/bin/pest packages/dashboard-reports/tests --configuration=phpunit.xml
```
