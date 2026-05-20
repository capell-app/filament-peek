# Batch 2 Package Demo Audit

Packages: `content-sections`, `dashboard-reports`, `demo-kit`, `deployments`, `diagnostics`, `document-lifecycle`, `email-studio`, `events`

Audit date: 2026-05-19

Harness copy: `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-2`

Baseline source: `/Users/ben/Sites/packages/capell/capell-package-demo-audit`

Core baseline packages: `capell-app/core`, `capell-app/admin`, `capell-app/frontend`, `capell-app/installer`, `capell-app/marketplace`

## Harness Notes

- Created an isolated batch harness by copying the existing baseline app to `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-2`.
- Processed packages one by one with Composer require, package discovery, migration, and Composer remove.
- Cleared only the copied harness bootstrap package cache between installs because a removed dependency can leave stale Laravel package manifest entries.
- The shared harness has known frontend asset warnings from `docs/internal/core-demo-baseline.md`; this first pass treats installs and route/surface mapping as verified, not final visual screenshot capture.
- After the install checks, the batch harness was returned to the five-package core baseline and `php artisan package:discover --ansi` passed.

## Commands Run

Package metadata and docs inspection:

```bash
sed -n '1,220p' docs/package-demo-audit-plan.md
sed -n '1,240p' docs/package-demo-audit-harness.md
sed -n '1,220p' docs/internal/core-demo-baseline.md
find packages/content-sections packages/dashboard-reports packages/demo-kit packages/deployments packages/diagnostics packages/document-lifecycle packages/email-studio packages/events -maxdepth 3 \( -name composer.json -o -name capell.json -o -name README.md -o -name overview.md -o -name screenshots.json \) -print | sort
find packages/content-sections packages/dashboard-reports packages/demo-kit packages/deployments packages/diagnostics packages/document-lifecycle packages/email-studio packages/events -maxdepth 4 -type d \( -path '*/src/Filament*' -o -path '*/resources/views*' -o -path '*/routes*' \) -print | sort
rg -n "(getSlug|getNavigationLabel|getNavigationGroup|getTitle|register|registerResource|registerPage|registerDashboardWidget|routes|loadRoutesFrom|->name\(|protected static|public static|navigation)" packages/content-sections/src packages/dashboard-reports/src packages/demo-kit/src packages/deployments/src packages/diagnostics/src packages/document-lifecycle/src packages/email-studio/src packages/events/src -g '*.php'
```

Harness install checks:

```bash
cp -a /Users/ben/Sites/packages/capell/capell-package-demo-audit /Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-2
cd /Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-2
composer require capell-app/content-sections:4.x-dev -W --no-interaction --no-scripts
php artisan package:discover --ansi
php artisan migrate --graceful --ansi
composer remove capell-app/content-sections capell-app/content-blocks capell-app/layout-builder -W --no-interaction --no-scripts
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php
```

Then repeated the same require/discover/migrate/remove pattern for:

```bash
capell-app/dashboard-reports:4.x-dev
capell-app/demo-kit:4.x-dev
capell-app/deployments:4.x-dev
capell-app/diagnostics:4.x-dev
capell-app/document-lifecycle:4.x-dev
capell-app/email-studio:4.x-dev
capell-app/events:4.x-dev
```

Focused package tests:

```bash
vendor/bin/pest packages/content-sections/tests --configuration=phpunit.xml
vendor/bin/pest packages/dashboard-reports/tests --configuration=phpunit.xml
vendor/bin/pest packages/demo-kit/tests --configuration=phpunit.xml
vendor/bin/pest packages/deployments/tests --configuration=phpunit.xml
vendor/bin/pest packages/diagnostics/tests --configuration=phpunit.xml
vendor/bin/pest packages/document-lifecycle/tests --configuration=phpunit.xml
vendor/bin/pest packages/email-studio/tests --configuration=phpunit.xml
vendor/bin/pest packages/events/tests --configuration=phpunit.xml
```

## Test Status

| Package              | Pest result                      |
| -------------------- | -------------------------------- |
| `content-sections`   | Passed: 82 tests, 530 assertions |
| `dashboard-reports`  | Passed: 9 tests, 26 assertions   |
| `demo-kit`           | Passed: 48 tests, 186 assertions |
| `deployments`        | Passed: 36 tests, 83 assertions  |
| `diagnostics`        | Passed: 83 tests, 211 assertions |
| `document-lifecycle` | Passed: 17 tests, 77 assertions  |
| `email-studio`       | Passed: 9 tests, 75 assertions   |
| `events`             | Passed: 20 tests, 65 assertions  |

## Package Findings

### content-sections

Composer name: `capell-app/content-sections`

Hard dependencies: `capell-app/admin`, `capell-app/content-blocks`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`

Optional dependencies installed: none

Install status: Composer require passed. It installed hard dependencies `content-blocks` and `layout-builder`; in this repo `capell-app/content-blocks` resolves to `packages/block-library`. `package:discover` and `migrate --graceful` passed in the batch harness.

Visible surfaces:

- Admin: `SectionResource`, `ListSections`, `CreateSection`, `EditSection`, `SectionAssetsRelationManager`, `SectionAlertsWidget`, `ModalTableSelect`.
- Frontend: Blade section block components under `capell-content-sections::components.section.blocks.*`.
- Runtime: Livewire admin helpers and public block payload contributor.

Docs updated:

- Added `packages/content-sections/docs/overview.md`.
- Added `packages/content-sections/docs/screenshots.json` with concrete screenshot paths, use cases, and seed-data notes.
- Updated `packages/content-sections/README.md` to list the actual hard dependencies and docs.

Screenshot gaps:

- Needs final seeded screenshots for section index/create/edit/assets, selector modal, and a frontend section gallery. Capture requires content-blocks and layout-builder installed.

Feature suggestions:

- Add a seeded section gallery command that creates one record per registered section block, so screenshot capture can prove every public block view without manual data setup.
- Add a section usage panel on `EditSection` showing which pages or blocks reference the section before editors change shared content.
- Add per-section preview URLs or preview actions that render the section in isolation with the current theme and language.
- Add a public safety regression test that renders every section block as an anonymous visitor and asserts no authoring markers, editor URLs, model IDs, or package internals are present.

### dashboard-reports

Composer name: `capell-app/dashboard-reports`

Hard dependencies: `capell-app/admin`, `capell-app/core`

Optional dependencies installed: none

Install status: Composer require passed. `package:discover` and `migrate --graceful` passed in the batch harness after clearing stale package cache from the previous install cycle.

Visible surfaces:

- Admin: `PublishingTrendChartWidget`, `ContentHealthWidget`, `DashboardReportsDashboardSettingsContributor`.
- Frontend: none.

Docs updated:

- Added `packages/dashboard-reports/docs/screenshots.json` with concrete screenshot paths, use cases, and widget visibility notes.
- Updated `packages/dashboard-reports/docs/overview.md` with admin-only/frontend-none and screenshot coverage notes.

Screenshot gaps:

- Needs final admin dashboard screenshots with seeded page states that make both widgets visible, including at least one content health issue.

Feature suggestions:

- Add drill-through links from each content health issue count to the filtered core page list.
- Add configurable stale-content thresholds per site or role, surfaced through the existing dashboard settings contributor.
- Add exportable report snapshots for weekly editorial review, using the same Action outputs as the widgets.
- Add a demo seed helper that creates scheduled, expired, URL-less, and stale pages so the widgets are screenshotable deterministically.

### demo-kit

Composer name: `capell-app/demo-kit`

Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`

Optional dependencies installed: none

Install status: Composer require passed. `package:discover` and `migrate --graceful` passed in the batch harness.

Visible surfaces:

- Admin: `DemoKitPage` at the `demo-kit` slug.
- Frontend: generated demo pages that render `capell-demo-kit::components.block.demo-page-content` and `capell-demo-kit::components.block.homepage-section`.
- Console: demo generation and doctor commands.

Docs updated:

- Added `packages/demo-kit/docs/screenshots.json` with concrete screenshot paths, use cases, and generated-content blockers.
- Updated `packages/demo-kit/docs/overview.md` with admin/frontend surfaces and screenshot coverage.

Screenshot gaps:

- Needs final screenshots for the admin page and seeded public demo pages using each package-owned block view. Public captures should prove presentation comes from package Blade, not stored database markup.

Feature suggestions:

- Add a small "screenshot profile" preset that generates deterministic low-volume content for package docs instead of full demo data.
- Add a Demo Kit run history panel to `DemoKitPage` showing seed, counts, packages dispatched, duration, and health result.
- Add an option to dry-run and preview the generated demo plan before writing records.
- Add package demo dependency validation that explains which package demo commands are skipped because optional packages are not installed.

### deployments

Composer name: `capell-app/deployments`

Hard dependencies: `capell-app/admin`, `capell-app/core`

Optional dependencies installed: none

Install status: Composer require passed. `package:discover` and `migrate --graceful` passed in the batch harness.

Visible surfaces:

- Admin: `DeploymentConnectionPage`, `DeploymentConnectionWidget`.
- HTTP: authenticated OAuth callback routes for GitHub, GitLab, and Bitbucket under `capell/oauth`.
- Frontend: none.

Docs updated:

- Updated `packages/deployments/docs/overview.md` with admin and runtime route surfaces.
- Updated `packages/deployments/docs/screenshots.json` to include `DeploymentConnectionWidget`, screenshot paths, use cases, and permission/demo-connection notes.

Screenshot gaps:

- Needs final screenshots for the connection page and widget with safe fake/demo connection data, or an explicit empty-state capture when no connection is seeded.

Feature suggestions:

- Add a connection test action on `DeploymentConnectionPage` that validates repository access without publishing a Composer change.
- Add provider-specific setup checklists for GitHub, GitLab, and Bitbucket OAuth settings beside the connection form.
- Add deployment dry-run output that shows the Composer diff and branch/PR target before a publish action runs.
- Add webhook or polling status for the last created deployment PR so admins can see whether install changes landed.

### diagnostics

Composer name: `capell-app/diagnostics`

Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/html-cache`

Optional dependencies installed: none

Install status: Composer require passed and installed hard dependency `html-cache`. `package:discover` and `migrate --graceful` passed in the batch harness.

Visible surfaces:

- Admin pages: `DiagnosticsPage`, `CommandPalettePage`, `SystemHealthPage`, `PermissionAuditPage`, `QueueHealthPage`.
- Admin widgets: cache, config drift, content graph, content, migrations, packages, registry, setup, site, Tailwind build status, and alerts health widgets.
- Console/runtime: dynamic command palette discovery and audited command execution.
- Frontend: none.

Docs updated:

- Updated `packages/diagnostics/docs/screenshots.json` to include missing pages, screenshot paths, use cases, role requirements, and host-state blockers.

Screenshot gaps:

- Final capture needs every diagnostics page and role-gated widget state. Some widgets need host migration, package, queue, cache, Tailwind, or content graph data before they show useful output.

Feature suggestions:

- Add "fix now" actions for low-risk diagnostics such as cache clearing or config cache rebuilds, with the existing command palette confirmation model.
- Add severity filtering and ownership tags to the diagnostics dashboard so agencies can separate hosting, code, content, and permissions issues.
- Add a downloadable diagnostics bundle for support that redacts secrets but includes package list, migration status, queue health, and recent command palette runs.
- Add widget-level docs links or remediation copy for each failing health check.

### document-lifecycle

Composer name: `capell-app/document-lifecycle`

Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/publishing-studio`

Optional dependencies installed: none

Install status: Composer require passed. It installed transitive packages needed by `publishing-studio`, including `navigation`, `migration-assistant`, and `html-cache`. `package:discover` and `migrate --graceful` passed in the batch harness.

Visible surfaces:

- Admin: `DocumentResource`, `ListDocuments`, `EditDocument`, `PublicationsRelationManager`, `AcceptancesRelationManager`.
- Database/runtime: document registration, publishing revision listener, publication records, acceptance records.
- Frontend: none registered by this package.

Docs updated:

- Added `packages/document-lifecycle/README.md`.
- Added `packages/document-lifecycle/docs/overview.md`.
- Added `packages/document-lifecycle/docs/screenshots.json` with concrete screenshot paths, use cases, and publication/acceptance seed-data notes.

Screenshot gaps:

- Needs final admin screenshots with at least one document, one publication, and one acceptance; a Publishing Studio-backed publication gives the best relation manager capture.

Feature suggestions:

- Add a document detail infolist or view page that summarizes latest publication, acceptance count, and linked documentable target without entering edit mode.
- Add version comparison support for adjacent document publications, using content hashes and Publishing Studio revision payloads.
- Add acceptance export filters by document key, version, context, acceptor type, and date range.
- Add a public helper/view component for rendering the active version of a controlled document without making the package own a full frontend route.

### email-studio

Composer name: `capell-app/email-studio`

Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`

Optional dependencies installed: none

Install status: Composer require passed. `package:discover` and `migrate --graceful` passed in the batch harness.

Visible surfaces:

- Admin: no package-owned Filament resource/page/widget in this implementation.
- Frontend: `routes/web.php` exists but is empty; no public route or Blade screen is registered in this implementation.
- Runtime: actions, job, provider adapter contracts, template/profile/message/recipient/event/reply/suppression/tracking models.

Docs updated:

- Added `packages/email-studio/docs/screenshots.json` with an empty required entry list and explicit blockers explaining why no screenshot target exists yet.
- Updated `packages/email-studio/docs/overview.md` to document the absence of visible UI surfaces.

Screenshot gaps:

- No screenshotable admin/frontend surface in this first pass. The package is service-level only until a Filament resource, widget, public route, or Blade screen ships.

Feature suggestions:

- Add a Filament template resource for registered templates and variants, with preview rendering for declared variables.
- Add an email message audit resource that lets support filter by recipient, provider, template, status, and site scope.
- Add suppression management UI with clear provenance, reason, and unblock workflow.
- Add provider diagnostics that send a safe test email and record adapter response metadata.

### events

Composer name: `capell-app/events`

Hard dependencies: `capell-app/admin`, `capell-app/frontend`, `capell-app/navigation`, `capell-app/publishing-studio`

Optional dependencies installed: none

Install status: Composer require passed. It installed transitive packages needed by `navigation` and `publishing-studio`, plus third-party recurrence/feed packages. `package:discover` and `migrate --graceful` passed in the batch harness.

Visible surfaces:

- Admin: `EventResource`, `EventVenueResource`, `EventOccurrenceResource`, `EventRegistrationResource`, `EventCalendarPage`, `EventCalendarWidget`.
- Frontend: `EventsListingPage`, `EventsCalendarPage`, `EventCalendar` Livewire component.
- HTTP: `events.ics` and `events/{listingPage}/feed.ics`.
- Runtime: recurrence expansion, event registration action, schema render hook.

Docs updated:

- Added `packages/events/docs/overview.md`.
- Added `packages/events/docs/screenshots.json` with concrete screenshot paths, use cases, public-output safety notes, and demo-data blockers.
- Updated `packages/events/README.md` to link the new docs.

Screenshot gaps:

- Needs final screenshots for event CRUD, venue/occurrence/registration management, admin calendar, admin widget, public listing/calendar pages, and feed route verification with seeded public occurrences and RSVP data.

Feature suggestions:

- Add a seeded event demo command that creates venues, recurring events, generated occurrences, and registrations for deterministic screenshots.
- Add conflict and capacity warnings on the event edit page when generated occurrences overlap or registrations exceed capacity.
- Add frontend structured data controls for event schema output, especially date, venue, offers, and registration availability.
- Add calendar feed filters by site, language, event type, and listing page so large sites can publish focused feeds.

## Coordinator Follow-Up

- Regenerate `docs/package-screenshot-manifest.json` after all batch manifests are merged.
- Merge selected suggestions into `docs/internal/package-feature-suggestions.md`; this worker intentionally did not edit that shared file.
- Merge verification and checklist rows into `docs/package-testing-audit.md`; this worker intentionally did not edit that shared file.
- Run final visual capture after the frontend asset generator issue from the core baseline is resolved.
