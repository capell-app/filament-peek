# Batch 5 Package Demo Audit

Date: 2026-05-19

Assigned packages: `password-policy`, `public-actions`, `publishing-studio`, `search`, `seo-suite`, `site-discovery`, `tags`, `theme-agency`.

## Harness

- Baseline copied from `/Users/ben/Sites/packages/capell/capell-package-demo-audit`.
- Batch harness: `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5`.
- Core baseline packages: `capell-app/core`, `capell-app/admin`, `capell-app/frontend`, `capell-app/installer`, `capell-app/marketplace`.
- Known baseline concern: the core harness now publishes the `capell-frontend` runtime manifest, but frontend/theme screenshots still need a Foundation Theme or package-specific theme stack so generated Tailwind CSS is present.
- Composer package install/discovery was verified one package at a time. Some package removals in the batch harness logged composer pre-uninstall event handler errors for previously removed package dependencies, but Composer continued and package discovery after each install was successful.
- After the install pass, the batch harness was returned to the five core baseline Capell packages.

## Verification Commands

```bash
cp -a /Users/ben/Sites/packages/capell/capell-package-demo-audit /Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5
composer require capell-app/password-policy:4.x-dev -W --no-interaction
rm -f database/database.sqlite && touch database/database.sqlite && php artisan migrate --graceful --ansi
php artisan capell:install --demo --package-mode=core --theme=none --url=http://127.0.0.1:8005 --name='Demo Admin' --email=admin@example.test --password=password --clear-cache --install-welcome-route --no-interaction
composer remove capell-app/password-policy --no-interaction -W && composer require capell-app/public-actions:4.x-dev -W --no-interaction
composer remove capell-app/public-actions --no-interaction -W && composer require capell-app/publishing-studio:4.x-dev -W --no-interaction
composer remove capell-app/publishing-studio capell-app/html-cache capell-app/migration-assistant capell-app/navigation --no-interaction -W && composer require capell-app/search:4.x-dev -W --no-interaction
composer remove capell-app/search --no-interaction -W && composer require capell-app/seo-suite:4.x-dev -W --no-interaction
composer remove capell-app/seo-suite capell-app/insights --no-interaction -W && composer require capell-app/site-discovery:4.x-dev -W --no-interaction
composer remove capell-app/site-discovery --no-interaction -W && composer require capell-app/tags:4.x-dev -W --no-interaction
composer remove capell-app/tags capell-app/publishing-studio capell-app/html-cache capell-app/migration-assistant capell-app/navigation --no-interaction -W && composer require capell-app/theme-agency:4.x-dev -W --no-interaction
composer remove capell-app/theme-agency capell-app/foundation-theme capell-app/layout-builder capell-app/content-blocks --no-interaction -W
composer show 'capell-app/*' --name-only
vendor/bin/pest packages/password-policy/tests --configuration=phpunit.xml
vendor/bin/pest packages/public-actions/tests --configuration=phpunit.xml
vendor/bin/pest packages/search/tests --configuration=phpunit.xml
vendor/bin/pest packages/site-discovery/tests --configuration=phpunit.xml
vendor/bin/pest packages/tags/tests --configuration=phpunit.xml
vendor/bin/pest packages/theme-agency/tests --configuration=phpunit.xml
vendor/bin/pest packages/foundation-theme/tests/Unit/ThemeTokenStoreTest.php --configuration=phpunit.xml
vendor/bin/pest packages/publishing-studio/tests --configuration=phpunit.xml
vendor/bin/pest packages/seo-suite/tests --configuration=phpunit.xml
jq empty packages/password-policy/docs/screenshots.json packages/public-actions/docs/screenshots.json packages/site-discovery/docs/screenshots.json
```

## Test Status

| Package                          | Pest status                                               |
| -------------------------------- | --------------------------------------------------------- |
| password-policy                  | Passed: 16 tests, 58 assertions                           |
| public-actions                   | Passed: 51 tests, 255 assertions                          |
| publishing-studio                | Passed: 549 tests, 548 passed, 1 skipped, 1520 assertions |
| search                           | Passed: 46 tests, 114 assertions                          |
| seo-suite                        | Passed: 236 tests, 1034 assertions                        |
| site-discovery                   | Passed: 70 tests, 274 assertions                          |
| tags                             | Passed: 28 tests, 136 assertions                          |
| theme-agency                     | Passed: 4 tests, 21 assertions                            |
| foundation-theme token filenames | Passed: 2 tests, 9 assertions                             |

## Package Audits

### password-policy

- Composer name: `capell-app/password-policy`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`.
- Optional dependencies: none.
- Install verification: Composer install and Laravel package discovery succeeded in the batch harness.
- Issue fixed: `PasswordPolicyServiceProvider` now registers the two package-tools migrations, so `vendor:publish --tag=capell-password-policy-migrations` can publish the users-table columns and password-history table.
- Admin surfaces: `PasswordPolicySettingsPage`, non-navigation `ForcedPasswordChangePage`, user form/table extenders for password status fields, filters, and require-password-change action.
- Frontend surfaces: none.
- Existing docs: `README.md` and `docs/overview.md` were accurate on package scope. `docs/screenshots.json` was missing.
- Docs changed: added `docs/screenshots.json`; linked it from README; clarified install persistence for Laravel migrations plus settings migrations.
- Screenshot coverage needed: settings page, forced password change form, and core user table extensions.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths.
- Screenshot capture: captured the settings page, forced password-change form, and core Users table password-policy extensions in `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/password-policy`.
- Harness notes: the admin panel required a Filament theme build at `public/build/filament/manifest.json`; the batch harness was updated with `make:filament-theme admin` and `buildDirectory: 'build/filament'` before screenshots were captured.

Feature suggestions:

- Add an admin policy simulator that shows why a selected user will or will not be forced through password change.
- Add an expiring-password notification workflow with configurable reminder windows before forced lockout.
- Add an audit export for password policy events, including forced-change flags, successful changes, and rejected reuse attempts.

### public-actions

- Composer name: `capell-app/public-actions`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`.
- Optional dependencies: `capell-app/access-gate`, `capell-app/form-builder`; not installed for this isolated pass.
- Install verification: Composer install and Laravel package discovery succeeded in the batch harness.
- Admin surfaces: `PublicActionResource`, `PublicActionDestinationResource`, `PublicActionSubmissionResource`, `PublicActionDispatchAttemptResource`, `PublicActionIntegrationTokenResource`.
- Frontend/API surfaces: `/actions/{action}` GET/POST and `/api/public-actions/zapier/*` API routes.
- Existing docs: README and integration docs existed, but no overview or screenshot contract.
- Docs changed: added `docs/overview.md`, added `docs/screenshots.json`, linked both from README.
- Screenshot coverage needed: all five resources, token modal, public form, and authenticated Zapier discovery response.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths.
- Screenshot capture: captured all eight manifest entries in `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/public-actions`.
- Public safety check: `curl http://127.0.0.1:8005/actions/demo-lead` returned no `admin`, `filament`, `livewire`, `public-actions`, `capell-public`, `model`, `editor`, `signed`, or `package` matches.
- Demo data: seeded one active action, one webhook destination, one handled submission, one successful dispatch attempt, and one Zapier token named `Demo Zapier token`.

Feature suggestions:

- Add a built-in test delivery action that sends a sample payload to a selected destination and records the dispatch attempt without a public submission.
- Add per-action submission schema preview and validation examples so admins can verify Zapier/API payloads before enabling the endpoint.
- Add destination health dashboards with failure rate, retryable attempts, and latest response snippets with secret redaction.

### publishing-studio

- Composer name: `capell-app/publishing-studio`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/html-cache`, `capell-app/migration-assistant`, `capell-app/navigation`.
- Optional dependencies: none.
- Install verification: Composer install and Laravel package discovery succeeded with all hard dependencies.
- Admin surfaces: workflow command center, activity trail, content scheduler, stale drafts, workspace resource, preview link management, page version history, relation managers on users, dashboard widgets, page table/header actions, and settings contributors.
- Frontend surfaces: preview exit route, scheduler iCal feed route, frontend workspace preview render hook/banner.
- Existing docs: README, overview, workflow docs, and screenshots contract already existed and are directionally accurate.
- Docs changed: screenshot manifest entries now include concrete use cases and deterministic output paths.
- Screenshot coverage needed: existing manifest should still be expanded later to include relation managers, action modals, scheduler iCal route assertion, and the frontend preview banner in anonymous/non-admin safety checks.

Feature suggestions:

- Add a reviewer workload view that groups pending review assignments by user, due date, and workspace risk level.
- Add a release impact preview that lists affected URLs, navigation changes, redirects, cache invalidations, and SEO checks before approval.
- Add a rollback drill mode that lets admins rehearse restore steps and see exactly which records/files would change.

### search

- Composer name: `capell-app/search`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`.
- Optional dependencies: none.
- Install verification: Composer install and Laravel package discovery succeeded in the batch harness.
- Admin surfaces: extension settings page, search settings schema, and dashboard widgets for overview stats, top searches, trending searches, and zero-result searches.
- Frontend surfaces: `/search` route, frontend search page, results component, form component, and header search render hook.
- Existing docs: README, overview, drivers/logging doc, and screenshot contract already existed.
- Issues fixed: public Search Blade no longer emits package-identifying `capell-search` classes; Search now registers a dedicated extension settings page; driver enum labels now translate to `Database` and `Scout`.
- Docs changed: screenshot manifest entries include concrete use cases and deterministic output paths; README/overview now document the extension settings page, flat database-driver index requirement, and core-only header screenshot harness note.
- Screenshot capture: captured all six manifest entries in `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/search`.
- Public safety check: `/search?q=search` and the header-search harness route returned no `admin`, `filament`, `livewire`, `capell-search`, `editor`, or `signed` matches.
- Harness notes: the database driver was mapped to a disposable `search_demo_pages` table because the default core demo `pages` table is not a flat searchable index; the header screenshot used a harness-only route because the core-only frontend has no theme header slot.
- Screenshot coverage needed: add a compatible-theme pass later for the Alpine modal version of the header search render hook, plus deliberate empty-state captures for each dashboard widget.

Feature suggestions:

- Add synonym and promoted-result management so editors can tune high-value queries without changing code.
- Add zero-result remediation workflow that turns frequent misses into suggested pages, redirects, or content tasks.
- Add privacy controls for query logging with visible retention, visitor hashing state, and purge history in the admin UI.

### seo-suite

- Composer name: `capell-app/seo-suite`.
- Hard dependencies: `capell-app/admin`, `capell-app/frontend`, `capell-app/insights`, `capell-app/site-discovery`.
- Optional dependencies: none.
- Install verification: Composer install and Laravel package discovery succeeded with hard dependencies.
- Admin surfaces: SEO audit, broken links, not-found URLs, translation coverage, AI Discovery, SEO settings, structured data settings, AI orchestrator settings, page/site schema extenders, page SEO widgets, site header AI Creator action, and dashboard widgets.
- Frontend surfaces: SEO head render hooks, canonical/social partials, schema components, AI discovery/Markdown outputs through package actions.
- Existing docs: README, overview, focused docs, and screenshots contract already existed.
- Issues fixed: `capell:seo-suite-install` now publishes all ten SEO Suite schema migrations, including AI creator, broken link, page SEO snapshot, and Search Console tables; `BrokenLinksPage` no longer uses the core `PageNameColumn` that expects a `pageable` morph relation.
- Docs changed: screenshot manifest entries now target concrete page/classes/routes instead of `BrokenLinksPage` placeholders; README/overview document Insights migration, Shield regeneration, and complete install-command requirements.
- Screenshot capture: captured SEO audit, broken links, not-found URLs, translation coverage, AI Discovery, SEO settings, page SEO panel, dashboard Search Console widgets, `/llms.txt`, `/robots.txt`, and `/index.md` in `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/seo-suite`.
- Public safety check: `/llms.txt`, `/llms-full.txt`, `/robots.txt`, `/index.md`, and `/` returned no `admin`, `filament`, `editor`, `signed`, `capell-seo-suite`, `capell-admin`, `livewire`, `model_id`, `page_id`, or `field` matches after replacing demo copy with visitor-facing text.
- Harness notes: SEO Suite requires dependency extensions to be installed before its Capell extension record can be installed; the disposable app also needed Insights migrations and `shield:generate --all --panel=admin` before all admin pages rendered.
- Screenshot coverage still needed: AI creator modal, site metadata fields, schema/head output inspection, and deliberate modal/action captures for AI Discovery table actions.

Feature suggestions:

- Add a publish-blocking SEO gate preview that shows exactly which checks would block publishing before editors submit work.
- Add a structured-data graph preview with validation warnings grouped by schema node.
- Add AI discovery freshness monitoring that flags stale Markdown/llms outputs after page or site metadata changes.
- Add an install preflight that checks hard dependency extension records, dependency tables, SEO Suite tables, and Shield permissions before exposing admin pages.

### site-discovery

- Composer name: `capell-app/site-discovery`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`.
- Optional dependencies: none.
- Install verification: Composer install and Laravel package discovery succeeded in the batch harness.
- Admin surfaces: package-added Sitemap actions on Page and Site resources, plus the sitemap generation Livewire tool.
- Frontend surfaces: `/sitemap` HTML sitemap page renderable/Livewire component and `/sitemap-xml` generated XML sitemap output.
- Existing docs: README existed, but no docs directory, overview, or screenshot contract.
- Docs changed: added `docs/overview.md`, added `docs/screenshots.json`, linked both from README.
- Issues fixed: `PagesSitemap` now caches serialized arrays and rebuilds `SitemapPageData` on read, avoiding `__PHP_Incomplete_Class` failures on `/sitemap`; public sitemap markup no longer exposes `capell-site-discovery` or `capell-sitemap` identifiers.
- Screenshot coverage: captured Page sitemap action, Site sitemap row action, sitemap generation tool, public HTML sitemap, and XML sitemap output in `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/site-discovery`.
- Public safety check: `/sitemap` and `/sitemap-xml` returned no `admin`, `filament`, `editor`, `signed`, `capell-site-discovery`, or `capell-sitemap` matches.
- Harness notes: the package was installed after the core baseline, so the default Sitemap page had to be created manually before browser capture. A package setup/backfill action should create Sitemap pages for existing sites after extension install.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths, and the XML entry targets `/sitemap-xml`.

Feature suggestions:

- Add an admin sitemap diff view showing URLs added, removed, or changed since the previous generation.
- Add robots.txt integration so sitemap URLs and crawler directives can be reviewed together.
- Add per-site sitemap health checks that verify generated files exist, are fresh, and only contain public discoverable URLs.

### tags

- Composer name: `capell-app/tags`.
- Hard dependencies: `capell-app/admin`, `capell-app/navigation`, `capell-app/publishing-studio`.
- Optional dependencies: none.
- Install verification: Composer install and Laravel package discovery succeeded with hard dependencies.
- Admin surfaces: `TagResource`, create/edit/list pages, pages relation manager, reusable `TagsInput`.
- Frontend surfaces: none registered directly.
- Existing docs: README, overview, and screenshot contract already existed.
- Issues fixed: Tags admin labels no longer reference `capell-layout-builder::*` translation keys, which leaked untranslated strings in a Tags-only install because Layout Builder is not a hard dependency.
- Docs changed: screenshot manifest entries now target `ListTags`, `CreateTag`, `EditTag:PagesRelationManager`, and `TagsInput`; README/overview now document the concrete admin routes, config publish path, dependency chain, and TagsInput host-form limitation.
- Screenshot capture: captured Tags index, create form, and tagged pages relation manager in `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/tags`.
- Harness notes: the Tags dependency chain required `html-cache`, `migration-assistant`, `navigation`, and `publishing-studio`; Publishing Studio migrations had to run before the taggable pivot could store the required `workspace_id`.
- Screenshot coverage still needed: capture `TagsInput` in a concrete host form, likely Blog article editing, because Tags provides the field but does not mount it independently.

Feature suggestions:

- Add tag merge and alias workflows to clean duplicate taxonomy terms safely.
- Add tag usage analytics showing which tags are active, orphaned, or overused across content types.
- Add scoped tag sets per site/content type so editors see only relevant taxonomy options in large installs.
- Add a small demo host form for `TagsInput` so the reusable component can be screenshot-tested without installing Blog.

### theme-agency

- Composer name: `capell-app/theme-agency`.
- Hard dependencies: `capell-app/core`, `capell-app/foundation-theme`; Composer also resolved `layout-builder` and `content-blocks` through the theme dependency chain.
- Optional dependencies: none.
- Install verification: Composer install and Laravel package discovery succeeded with hard dependencies.
- Admin surfaces: no package-owned Filament navigation, resource, page, or settings. Agency appears through core/foundation theme selection.
- Frontend surfaces: `agency` theme renderer, page wrapper, and section views for navigation, hero, features, proof, content listing, CTA, and footer.
- Existing docs: README, overview, and screenshot contract already existed.
- Issues fixed: public wrapper and section views no longer emit `data-capell-theme` or `capell-theme-*` classes; theme token CSS filenames are now opaque and no longer include theme or preset keys.
- Harness setup: removed stale enabled extension rows from earlier package passes, installed Layout Builder before `capell:foundation-theme-setup`, created an Agency theme record, set Theme Studio to `agency`/`signal`, installed the Foundation Theme npm dependencies, and built frontend plus Filament assets.
- Docs changed: screenshot manifest entries now point at `ThemeResource:index`, `/theme-agency-demo`, and `capell.admin.theme-preview`; README and overview document the setup pitfalls above.
- Screenshots captured:
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-agency/theme-admin-list-showing-agency.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-agency/frontend-page-rendered-with-agency-theme.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-agency/theme-preview-url-output.png`
- Public safety check: `/theme-agency-demo` rendered successfully and the HTML body scan found no `capell-theme`, `data-capell-theme`, `theme-agency`, `signed`, `filament`, `editor`, or `/admin`.

Feature suggestions:

- Add packaged demo content for each Agency preset so screenshots can exercise every section view deterministically without a harness-only route.
- Add theme health checks for missing preview images, missing compiled agency CSS, missing Foundation Theme npm dependencies, and stale Theme Studio presets from a different theme.
- Add a theme comparison preview that renders the same page through Foundation and Agency side-by-side for editors choosing a theme.
