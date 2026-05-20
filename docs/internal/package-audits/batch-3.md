# Batch 3 Package Demo Audit

Captured: 2026-05-19
Worker scope: `form-builder`, `foundation-theme`, `frontend-authoring`, `frontend-optimizer`, `ga4-reports`, `hero`, `html-cache`, `insights`

## Harness Status

- Batch harness path: `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-3`
- Baseline copied from: `/Users/ben/Sites/packages/capell/capell-package-demo-audit`
- Composer install and `capell:extension-install` were verified in the batch copy, resetting from the baseline copy between package checks.
- Visual screenshot capture was not finalized in this pass. The shared baseline now publishes `public/vendor/capell-frontend/manifest.json`, but frontend/theme screenshots still need a Foundation Theme or package-specific theme stack so generated Tailwind CSS is present.
- Dependency-order note: packages with hard dependencies outside the core baseline must have those dependencies installed and marked installed first. `foundation-theme` and `hero` require `layout-builder`; `frontend-authoring` requires `html-cache`.

## Package Results

### form-builder

- Composer: `capell-app/form-builder`
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`
- Install check: `composer require capell-app/form-builder:4.x-dev -W --no-interaction` passed; `php artisan capell:extension-install capell-app/form-builder --no-interaction` marked the package installed.
- Admin surfaces: `SubmissionResource`, `ListSubmissions`, reports navigation group.
- Frontend surfaces: `FormComponent`, `FormElementComponent`, renderables `capell-form-builder::block.form` and field renderable.
- Existing docs issue fixed: README and overview now identify the submissions admin resource instead of saying no admin surface was proven.
- Screenshot contract status: `docs/screenshots.json` now includes concrete use cases and deterministic output paths; final image capture still needs demo form/submission data.
- Package tests: `vendor/bin/pest packages/form-builder/tests --configuration=phpunit.xml` passed, 62 tests / 274 assertions.

Feature suggestions:

- Add a demo seeder that creates one active contact form, one spam submission, one unread submission, and one archived submission so screenshots show the full submissions workflow.
- Add a submission detail/read-mode screenshot contract that captures payload rendering and reply affordances without exposing encrypted raw storage details.
- Add a privacy settings helper surface that makes IP/user-agent collection and retention defaults visible to site operators.

### foundation-theme

- Composer: `capell-app/foundation-theme`
- Hard dependencies: `capell-app/frontend`, `capell-app/layout-builder`
- Install check: initial target-only install was blocked by missing installed `layout-builder`; re-run with `layout-builder` installed first passed and marked the package installed.
- Admin surfaces: `FoundationThemeSettingsSchema`, theme chrome header/footer registrations, `header` Layout Builder area.
- Frontend surfaces: `capell` Blade namespace, anonymous `capell::...` components, Foundation header/footer, layout/page/block components, media components, runtime CSS tokens.
- Existing docs issue fixed: README and overview now include `layout-builder` as a hard dependency; overview now documents settings/theme chrome/header area surfaces.
- Screenshot contract status: includes concrete use cases and deterministic output paths, including the Foundation header Layout Builder area.
- Package tests: `vendor/bin/pest packages/foundation-theme/tests --configuration=phpunit.xml` passed, 88 tests / 345 assertions.

Feature suggestions:

- Add a first-party Foundation demo fixture that seeds a page, header area block, footer menu, language switcher, and representative media so theme screenshots are reproducible.
- Add a theme settings preview screen or action showing token changes before applying them to public pages.
- Add a frontend safety diagnostic that explicitly checks Foundation-rendered HTML for accidental authoring metadata after integrating with frontend-authoring.

### frontend-authoring

- Composer: `capell-app/frontend-authoring`
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/html-cache`
- Install check: initial target-only install was blocked by missing installed `html-cache`; re-run with `html-cache` installed first passed and marked the package installed.
- Admin surfaces: no Filament navigation page; signed admin editor route and Livewire `EditRegionField`.
- Frontend surfaces: `POST /beacon`, signed `GET /authoring/regions/{payload}`, beacon bootstrap, editable region manifest, admin-only overlay.
- Existing docs status: README, overview, in-page editing docs, and screenshot contract already describe the admin-only beacon model and public safety requirements.
- Screenshot contract status: existing contract covers admin-decorated desktop/mobile states, beacon network request, and anonymous/no-authoring assertions; entries now include concrete use cases and deterministic output paths.
- Package tests: `vendor/bin/pest packages/frontend-authoring/tests --configuration=phpunit.xml` passed, 25 tests / 163 assertions.

Feature suggestions:

- Add a screenshot/browser contract for a non-admin authenticated user proving the beacon response still contains no authoring surface.
- Add an admin audit log entry for inline edits that records region key, model class, field, and cache-clearing outcome.
- Add a registry diagnostics page or Site Health panel showing registered editable regions and selectors without exposing them to public output.

### frontend-optimizer

- Composer: `capell-app/frontend-optimizer`
- Hard dependencies: `capell-app/core`, `capell-app/frontend`
- Install check: Composer install and `capell:extension-install` passed; package marked installed.
- Admin surfaces: none.
- Frontend surfaces: `@frontendOptimizerAssets(...)` Blade directive, render profile asset output, critical CSS generation artifacts.
- Existing docs issue fixed: added overview and screenshots contract; README now calls out the directive and lack of admin/public route surface.
- Screenshot contract status: `docs/screenshots.json` covers profile asset output and critical CSS artifacts with concrete use cases and deterministic output paths.
- Package tests: `vendor/bin/pest packages/frontend-optimizer/tests --configuration=phpunit.xml` passed, 18 tests / 46 assertions.

Feature suggestions:

- Add an optional admin diagnostics panel listing render profiles, last optimization run, asset counts, and stale profile state.
- Add a demo theme fixture that calls `@frontendOptimizerAssets(...)` so frontend screenshots can prove emitted assets without custom setup.
- Add a CLI report command that prints profile keys, referenced assets, critical CSS path, and last generation status for deployment debugging.

### ga4-reports

- Composer: `capell-app/ga4-reports`
- Hard dependencies: `capell-app/admin`, `capell-app/core`
- Install check: Composer install and `capell:extension-install` passed; package marked installed.
- Admin surfaces: `GA4ReportsPage`, overview stats, traffic trend widget, top pages widgets, setup status widget, settings schema.
- Frontend surfaces: none.
- Existing docs issue fixed: added screenshots contract and admin surface section to overview.
- Screenshot contract status: `docs/screenshots.json` covers dashboard page, setup status, and settings with concrete use cases and deterministic output paths.
- Package tests: `vendor/bin/pest packages/ga4-reports/tests --configuration=phpunit.xml` passed, 18 tests / 106 assertions.

Feature suggestions:

- Add a fake GA4 demo data seeder so dashboard screenshots show trend and top-page states without live credentials.
- Add a sync health widget showing last sync status, row counts, and last error with an explicit retry action.
- Add a configuration validation action that checks credentials path readability and property ID format before enabling sync.

### hero

- Composer: `capell-app/hero`
- Hard dependencies: `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`
- Install check: initial target-only install was blocked by missing installed `layout-builder`; re-run with `layout-builder` installed first passed and marked the package installed.
- Admin surfaces: none directly; editors see Hero through Layout Builder content after setup/demo data.
- Frontend surfaces: `capell::block.hero`, anonymous `capell-hero` components, hero Tailwind sources, setup command.
- Existing docs issue fixed: README now includes frontend component surface and `layout-builder`; added overview and screenshots contract.
- Screenshot contract status: `docs/screenshots.json` covers the home hero block and optional slide/related variant with concrete use cases and deterministic output paths.
- Package tests: `vendor/bin/pest packages/hero/tests --configuration=phpunit.xml` passed, 3 tests / 19 assertions.

Feature suggestions:

- Add a demo fixture with one standard hero, one media hero, and one related-content hero state for screenshot generation.
- Add Layout Builder metadata documenting which editable fields feed the hero block so editor-facing schema and frontend output remain aligned.
- Add an accessibility-focused view test for heading level, CTA link names, image alt handling, and reduced-motion carousel behavior if slides are introduced.

### html-cache

- Composer: `capell-app/html-cache`
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`
- Install check: Composer install and `capell:extension-install` passed; package marked installed.
- Admin surfaces: `MaintenanceCachePage`, `CachedModelUrlResource`, dashboard widgets, page table extender, site header action extender, Site Health cache map.
- Frontend surfaces: cache middleware, anonymous cache hit behavior, static maintenance page store/output.
- Existing docs issue fixed: added overview and screenshots contract; README now documents maintenance page, widgets, and extenders.
- Screenshot contract status: `docs/screenshots.json` covers maintenance cache, cached URLs, dashboard widgets, Site Health, page-table extension, public cache hit, and maintenance page with concrete use cases and deterministic output paths.
- Package tests: `vendor/bin/pest packages/html-cache/tests --configuration=phpunit.xml` passed, 78 tests / 283 assertions.

Feature suggestions:

- Add a cache warmup demo command that generates one cached page, one dependency map, and one stale queue row for screenshot fixtures.
- Add a public safety audit dashboard card that summarizes scanned cached files and flags any authoring markers or unindexed files.
- Add per-site cache policy presets in settings so operators can choose scheduled invalidation, immediate invalidation, or static export behavior without editing config.

### insights

- Composer: `capell-app/insights`
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`
- Install check: Composer install and `capell:extension-install` passed; package marked installed.
- Admin surfaces: `InsightsPage`, overview stats, live stats, popular pages, trending pages, recent journeys, top actions, settings schema.
- Frontend surfaces: `POST /capell/insights/events`, `POST /capell/insights/consent`, render hook tracker script.
- Existing docs issue fixed: README and overview now identify the extension page, widgets, settings schema, and overview stats.
- Screenshot contract status: existing contract covers dashboard widgets, settings, and frontend tracker with concrete use cases and deterministic output paths; it should still add explicit beacon/consent route proof in a later pass.
- Package tests: `vendor/bin/pest packages/insights/tests --configuration=phpunit.xml` passed, 77 tests / 387 assertions.

Feature suggestions:

- Add a deterministic analytics demo seeder with visits, clicks, consent states, and journey sequences for meaningful dashboard screenshots.
- Add a consent-mode screenshot/browser contract proving events are suppressed or reduced according to region and consent settings.
- Add a retention health widget showing oldest stored visit, purge schedule status, and estimated storage volume.

## Commands Run

```bash
composer show 'capell-app/*' --name-only
composer require capell-app/form-builder:4.x-dev -W --no-interaction
php artisan capell:extension-install capell-app/form-builder --no-interaction --ansi
php artisan migrate --graceful --ansi
php artisan package:discover --ansi
php artisan capell:doctor --ansi
php artisan list --raw | rg '^capell|package|marketplace|extension'
php artisan capell:extension-install --help
composer require capell-app/{package}:4.x-dev -W --no-interaction
php artisan capell:extension-install capell-app/{package} --no-interaction --ansi
composer require capell-app/layout-builder:4.x-dev capell-app/foundation-theme:4.x-dev -W --no-interaction
composer require capell-app/html-cache:4.x-dev capell-app/frontend-authoring:4.x-dev -W --no-interaction
composer require capell-app/layout-builder:4.x-dev capell-app/hero:4.x-dev -W --no-interaction
php artisan capell:extension-install capell-app/{hard-dependency} --no-interaction --ansi
php artisan capell:extension-install capell-app/{target-package} --no-interaction --ansi
vendor/bin/pest packages/form-builder/tests --configuration=phpunit.xml
vendor/bin/pest packages/foundation-theme/tests --configuration=phpunit.xml
vendor/bin/pest packages/frontend-authoring/tests --configuration=phpunit.xml
vendor/bin/pest packages/frontend-optimizer/tests --configuration=phpunit.xml
vendor/bin/pest packages/ga4-reports/tests --configuration=phpunit.xml
vendor/bin/pest packages/hero/tests --configuration=phpunit.xml
vendor/bin/pest packages/html-cache/tests --configuration=phpunit.xml
vendor/bin/pest packages/insights/tests --configuration=phpunit.xml
```

## Concerns For Coordinator

- Final frontend/theme screenshot publication should use a package-specific theme baseline with generated Tailwind CSS.
- `docs/package-screenshot-manifest.json` was not regenerated in this worker pass because batch instructions limited edits to package docs and this batch report.
- The copied harness ended on the last dependency install state; rerun from the shared baseline copy before the next batch action.
