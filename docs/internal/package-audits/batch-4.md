# Batch 4 Package Demo Audit

Captured: 2026-05-19

Worker scope: `layout-builder`, `login-audit`, `media-ai`, `media-library`, `migration-assistant`, `navigation`, `newsletter`, `notes`.

Harness used: `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-4`, copied from the shared baseline app and reset between package installs. The shared baseline app was not mutated.

## Summary

| Package               | Install check                     | Tests             | Visible surfaces                      | Docs action                                                                                                    |
| --------------------- | --------------------------------- | ----------------- | ------------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| `layout-builder`      | Passed                            | Passed, 145 tests | Admin, frontend, console              | Added overview and screenshot contract                                                                         |
| `login-audit`         | Passed with Laravel 13 fork alias | Passed, 30 tests  | Admin                                 | Uses `fdemb/laravel-authentication-log` PR #140 as a root app alias until upstream releases Laravel 13 support |
| `media-ai`            | Passed                            | Passed, 3 tests   | Admin action extender, console health | Fixed stale overview title and screenshot output path                                                          |
| `media-library`       | Passed                            | Passed, 22 tests  | Admin, console migration command      | Existing docs retained                                                                                         |
| `migration-assistant` | Passed                            | Passed, 132 tests | Admin, console                        | Fixed docs dependency line                                                                                     |
| `navigation`          | Passed                            | Passed, 108 tests | Admin, frontend, console              | Existing docs retained                                                                                         |
| `newsletter`          | Passed                            | Passed, 26 tests  | Admin, frontend, console/schedule     | Added overview and screenshot contract                                                                         |
| `notes`               | Passed                            | Passed, 26 tests  | Admin                                 | Added overview and screenshot contract                                                                         |

## Commands Run

Package repo:

```bash
vendor/bin/pest packages/layout-builder/tests --configuration=phpunit.xml
vendor/bin/pest packages/login-audit/tests --configuration=phpunit.xml
vendor/bin/pest packages/media-ai/tests --configuration=phpunit.xml
vendor/bin/pest packages/media-library/tests --configuration=phpunit.xml
vendor/bin/pest packages/migration-assistant/tests --configuration=phpunit.xml
vendor/bin/pest packages/navigation/tests --configuration=phpunit.xml
vendor/bin/pest packages/newsletter/tests --configuration=phpunit.xml
vendor/bin/pest packages/notes/tests --configuration=phpunit.xml
php -r 'json_decode(file_get_contents($argv[1]), true, flags: JSON_THROW_ON_ERROR); echo $argv[1]." ok\n";' packages/layout-builder/docs/screenshots.json
php -r 'json_decode(file_get_contents($argv[1]), true, flags: JSON_THROW_ON_ERROR); echo $argv[1]." ok\n";' packages/media-ai/docs/screenshots.json
php -r 'json_decode(file_get_contents($argv[1]), true, flags: JSON_THROW_ON_ERROR); echo $argv[1]." ok\n";' packages/newsletter/docs/screenshots.json
php -r 'json_decode(file_get_contents($argv[1]), true, flags: JSON_THROW_ON_ERROR); echo $argv[1]." ok\n";' packages/notes/docs/screenshots.json
```

Batch harness:

```bash
cp -a /Users/ben/Sites/packages/capell/capell-package-demo-audit /Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-4
composer show 'capell-app/*' --locked --format=json
composer install --no-interaction --no-progress --ansi
php artisan migrate --graceful --ansi
php artisan capell:install --demo --package-mode=core --theme=none --url=http://127.0.0.1:8000 --name="Demo Admin" --email=admin@example.test --password=password --clear-cache --install-welcome-route --no-interaction
composer require capell-app/{package}:4.x-dev -W --no-interaction --no-progress --ansi
php artisan package:discover --ansi
php artisan migrate --graceful --ansi
composer show "capell-app/*" --locked
```

The restore/install/discover/migrate sequence was run once per package.

## Package Notes

### layout-builder

- Composer name: `capell-app/layout-builder`.
- Hard dependencies observed: `capell-app/admin`, `capell-app/content-blocks`, `capell-app/core`, `capell-app/frontend`.
- Manifest mismatch: `capell.json` lists only core/frontend under `dependencies.requires`, but Composer requires admin/content-blocks as well.
- Install verification: passed in the batch harness; `package:discover` and migrations returned status 0.
- Admin surfaces: `BlockResource`, Layout Builder `LayoutResource`, page/layout schema extenders, content-first/layout-first Livewire editor, Filament assets, layout health/recent activity widgets when enabled.
- Frontend surfaces: public layout components, main-content render, named layout areas, public layout graph action; no standalone route.
- Docs comparison: README existed; `docs/overview.md` and `docs/screenshots.json` were missing and were added.
- Public safety: public rendering docs now call out no DB queries, editor state, signed URLs, field paths, admin labels, internal IDs, or diagnostics in anonymous HTML.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths; public entries must still be captured anonymously against seeded layout data.

Feature suggestions:

| Suggestion                                                                           | User value                                                                  | Implementation risk | Package fit                              |
| ------------------------------------------------------------------------------------ | --------------------------------------------------------------------------- | ------------------- | ---------------------------------------- |
| Add seeded screenshot/demo fixtures for content-first and layout-first editor states | Makes demo screenshots reproducible and easier to compare                   | Medium              | Directly tied to editor surfaces         |
| Add a package manifest health check for Composer/capell.json dependency drift        | Prevents marketplace installs from omitting hard dependencies               | Low                 | Manifest already exposes dependencies    |
| Add a public-render safety audit command for blocks/layout areas                     | Gives maintainers quick proof that frontend output has no authoring leakage | Medium              | Directly tied to public layout rendering |

### login-audit

- Composer name: `capell-app/login-audit`.
- Hard dependencies declared: `capell-app/admin`; Composer also requires `rappasoft/laravel-authentication-log` and `tapp/filament-authentication-log`.
- Install verification: passed in the Laravel 13 harness after adding `https://github.com/fdemb/laravel-authentication-log` as a root VCS repository and requiring `rappasoft/laravel-authentication-log:dev-main as 6.0.1` with `capell-app/login-audit`.
- Package tests: passed.
- Admin surfaces: `LoginAuditResource`, user relation manager, dashboard widget, login audit settings contributor/schema, auth/user activity middleware.
- Frontend surfaces: none declared.
- Docs comparison: overview and screenshots manifest were tightened after the Laravel 13 install blocker was resolved.
- Screenshot capture: admin index, table filters, settings, dashboard/widget configuration, user edit summary, and user relation use case were captured in the shared Laravel 13 harness. The user relation surface requires the host user model to use Rappasoft's `AuthenticationLoggable` trait.
- Public safety: no public surface observed.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths.

Feature suggestions:

| Suggestion                                                                             | User value                                                                    | Implementation risk | Package fit                      |
| -------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------- | ------------------- | -------------------------------- |
| Add a Laravel 13-compatible authentication log adapter or upstream compatibility guard | Unblocks installation in the current Capell 4 harness                         | Medium              | Core package dependency issue    |
| Add suspicious-login saved filters to the resource                                     | Helps operators find new devices, failed bursts, and impossible travel faster | Low                 | Uses existing log data           |
| Add CSV export with redaction controls                                                 | Supports security reviews without over-sharing user agents/IP data            | Medium              | Natural extension of audit table |

### media-ai

- Composer name: `capell-app/media-ai`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`.
- Optional integration: an AI package can bind `Capell\MediaAI\Contracts\ImageDoctor`; default binding is `NullImageDoctor`.
- Install verification: passed in the batch harness; `package:discover` and migrations returned status 0.
- Admin surfaces: `MediaAIEditActionExtender` adds a `Doctor image` action to media edit pages only when the record is an image and a non-null image doctor is bound.
- Frontend surfaces: none.
- Docs comparison: fixed stale `Media AIOrchestrator` wording and screenshot output path.
- Public safety: no public surface observed.

Feature suggestions:

| Suggestion                                                           | User value                                                        | Implementation risk | Package fit                             |
| -------------------------------------------------------------------- | ----------------------------------------------------------------- | ------------------- | --------------------------------------- |
| Add a provider-readiness admin indicator on the media edit action    | Explains why the action is hidden until an image doctor is bound  | Low                 | Matches current visibility guard        |
| Add operation presets with capability checks from the bound provider | Prevents editors selecting unsupported image operations           | Medium              | Uses existing structured request object |
| Add generated-image provenance metadata                              | Helps teams distinguish original, edited, and AI-generated assets | Medium              | Natural media workflow extension        |

### media-library

- Composer name: `capell-app/media-library`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `awcodes/filament-curator`.
- Install verification: passed in the batch harness; `package:discover` and migrations returned status 0.
- Admin surfaces: `MediaHealthPage` at `media-health`, Curator media field factory, Curator media model wrapper.
- Frontend surfaces: no standalone public route; tests cover frontend render integration.
- Console/runtime surfaces: Spatie-to-Curator migration command/action.
- Docs comparison: existing overview and screenshots manifest cover the primary Media Health page, table, Curator field, and migration report intent.
- Public safety: no direct public output beyond media field/render integrations.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths.

Feature suggestions:

| Suggestion                                                                         | User value                                                                      | Implementation risk | Package fit                        |
| ---------------------------------------------------------------------------------- | ------------------------------------------------------------------------------- | ------------------- | ---------------------------------- |
| Add seeded health-state demos for missing alt text, stale media, and unused assets | Makes the Media Health screenshot meaningful instead of empty                   | Low                 | Directly tied to MediaHealthPage   |
| Add a dry-run migration report view for Spatie-to-Curator migration                | Lets teams preview risk before moving assets                                    | Medium              | Fits existing migration action     |
| Add bulk remediation actions from Media Health                                     | Turns audit findings into workflow, such as mark decorative or request alt text | Medium              | Uses existing health table surface |

### migration-assistant

- Composer name: `capell-app/migration-assistant`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`.
- Install verification: passed in the batch harness; `package:discover` and migrations returned status 0.
- Admin surfaces: `ImportSessionResource`, `ImportPagesPage`, `ImportSitesPage` contribution. `ImportSitesPage` is registered as a contributed page but `shouldRegisterNavigation()` returns false and it is currently a stub.
- Frontend surfaces: none.
- Console/runtime surfaces: queued import execution, package reader/writer, import validation, relation resolution, media ingest, rollback reporting.
- Docs comparison: overview had a stale dependency line listing only core; updated to admin/core.
- Public safety: no public surface observed.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths.

Feature suggestions:

| Suggestion                                                                                  | User value                                                                                                    | Implementation risk | Package fit                               |
| ------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------- | ------------------- | ----------------------------------------- |
| Add seeded import sessions for each wizard step                                             | Enables full screenshot coverage of upload, review, resolve, validate, executing, complete, and failed states | Medium              | Directly tied to ImportPagesPage          |
| Replace the hidden/stub site-import page with an explicit disabled state in Recovery Center | Reduces confusion for operators and screenshot runners                                                        | Low                 | Current page is present but not navigable |
| Add a rollback rehearsal mode                                                               | Lets operators understand what rollback can and cannot undo before running imports                            | Medium              | Fits rollback report model                |

### navigation

- Composer name: `capell-app/navigation`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`.
- Install verification: passed in the batch harness; `package:discover` and migrations returned status 0.
- Admin surfaces: `NavigationResource`, create/edit/list pages, Site relation manager, Page navigation tab/schema extender, navigation configurator.
- Frontend surfaces: menu components and header/menu Blade components; no standalone public route.
- Console surfaces: `capell:navigation-setup`, `capell:navigation-demo`.
- Docs comparison: existing overview and screenshots manifest cover admin resource, form, site relation manager, page tab, and frontend menu output.
- Public safety: frontend menu output should be seeded and checked to ensure no page/internal model IDs or editor metadata appear.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths.

Feature suggestions:

| Suggestion                                                                       | User value                                                 | Implementation risk | Package fit                                     |
| -------------------------------------------------------------------------------- | ---------------------------------------------------------- | ------------------- | ----------------------------------------------- |
| Add seeded frontend menu screenshot fixtures with nested page/link/heading items | Makes frontend screenshot coverage representative          | Low                 | Directly tied to menu components                |
| Add stale-reference repair actions in the navigation resource                    | Helps editors fix deleted/moved page references            | Medium              | Current model stores page references in JSON    |
| Add per-site navigation cache invalidation diagnostics                           | Makes cache behaviour transparent when menus do not update | Low                 | Manifest already declares navigation cache tags |

### newsletter

- Composer name: `capell-app/newsletter`.
- Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/form-builder`, `capell-app/frontend`, `capell-app/tags`.
- Install verification: passed in the batch harness; `package:discover` and migrations returned status 0. Composer installed form-builder and tags with the package.
- Admin surfaces: Subscriber, Provider Connection, Provider Audience, Provider Interest Mapping, Form Mapping, Newsletter Tag, Segment, Import Batch, and Sync Attempt resources; overview stats; newsletter settings schema.
- Frontend surfaces: `/newsletter/confirm/{token}`, `/newsletter/unsubscribe/{token}`, `/newsletter/providers/{providerConnection}/webhook`.
- Console/scheduled surfaces: `newsletter:sync-retry-due`, scheduled every five minutes.
- Docs comparison: README existed but no overview or screenshots manifest; both were added and README docs links were updated.
- Public safety: public route screenshots must use disposable tokens and must not expose provider secrets, subscriber internals, admin labels, or form mapping details.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths.

Feature suggestions:

| Suggestion                                                | User value                                                  | Implementation risk | Package fit                                              |
| --------------------------------------------------------- | ----------------------------------------------------------- | ------------------- | -------------------------------------------------------- |
| Add demo seeders for full subscriber/provider/sync states | Makes nine admin resource screenshots useful and repeatable | Medium              | Directly tied to package surface breadth                 |
| Add webhook delivery audit and replay UI                  | Helps operators diagnose provider sync failures             | Medium              | Fits provider webhook and sync attempt models            |
| Add segment preview counts before saving                  | Lets marketers validate segment logic before syncing        | Medium              | Natural extension of SegmentResource and segment actions |

### notes

- Composer name: `capell-app/notes`.
- Hard dependencies: `capell-app/admin`.
- Install verification: passed in the batch harness; `package:discover` and migrations returned status 0.
- Admin surfaces: `NotesInboxPage` at `/admin/notes`, user-menu item with attention badge, attention count actions.
- Frontend surfaces: none.
- Docs comparison: README existed but explicitly said no deeper docs; added overview and screenshots manifest, and updated README docs links.
- Public safety: no public surface observed.
- Deep screenshot pass: manifest entries now include concrete use cases and deterministic output paths.

Feature suggestions:

| Suggestion                                                                 | User value                                                 | Implementation risk | Package fit                                            |
| -------------------------------------------------------------------------- | ---------------------------------------------------------- | ------------------- | ------------------------------------------------------ |
| Add attachable note panels to core admin records via a documented extender | Makes contextual notes discoverable where work happens     | Medium              | Matches package purpose but needs resource integration |
| Add seeded reminder/mention/assignment demo states                         | Makes inbox and user-menu badge screenshots representative | Low                 | Directly tied to current page and badge                |
| Add notification preferences for note mentions and reminders               | Reduces notification noise for active admin teams          | Medium              | Fits collaboration workflow                            |

## Cross-Package Follow-Ups

- Align `capell.json` hard dependencies with Composer for `layout-builder`.
- Resolve `login-audit` Laravel 13 compatibility before final screenshot capture.
- Seed package-specific demo data before final screenshot publication; empty admin tables will not prove the intended workflows.
- Keep public screenshots for `layout-builder`, `navigation`, and `newsletter` focused on anonymous/non-admin safety.
