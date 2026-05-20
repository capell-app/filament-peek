# Batch 6 Package Audit

Date: 2026-05-19
Worker: isolated first pass
Batch harness: `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-6`

## Summary

Audited packages: `theme-corporate`, `theme-saas`, `translation-manager`, `welcome-tour`, `wordpress-importer`.

The batch harness was created by copying the existing core demo baseline to the batch-6 path, then resetting it from that baseline between package installs. The Theme Corporate pass was revisited in the batch-5 harness to fix public-output leaks, capture screenshots, and update docs.

## Commands Run

- `sed -n '1,220p' docs/package-demo-audit-plan.md`
- `sed -n '1,260p' docs/package-demo-audit-harness.md`
- `find packages/theme-corporate packages/theme-saas packages/translation-manager packages/welcome-tour packages/wordpress-importer -maxdepth 3 ...`
- `rg -n "Filament|Resource|Page|routes|Route::|register|view\\(|render|navigation|Settings|livewire|->" ...`
- `rsync -a --delete --exclude='node_modules' /Users/ben/Sites/packages/capell/capell-package-demo-audit/ /Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-6/`
- `composer install --no-interaction` in the batch harness
- `composer require capell-app/theme-corporate:4.x-dev -W --no-interaction`
- `php artisan capell:extension-install capell-app/layout-builder --no-interaction --url=http://127.0.0.1:8000`
- `php artisan capell:extension-install capell-app/foundation-theme --no-interaction --url=http://127.0.0.1:8000`
- `php artisan capell:extension-install capell-app/theme-corporate --no-interaction --url=http://127.0.0.1:8000`
- `composer require capell-app/theme-saas:4.x-dev -W --no-interaction`
- `php artisan capell:extension-install capell-app/theme-saas --no-interaction --url=http://127.0.0.1:8000`
- `composer require capell-app/translation-manager:4.x-dev -W --no-interaction`
- `php artisan capell:extension-install capell-app/translation-manager --no-interaction --url=http://127.0.0.1:8000`
- `composer require capell-app/welcome-tour:4.x-dev -W --no-interaction`
- `php artisan capell:extension-install capell-app/welcome-tour --no-interaction --url=http://127.0.0.1:8000`
- `composer require capell-app/wordpress-importer:4.x-dev -W --no-interaction`
- `php artisan capell:extension-install capell-app/migration-assistant --no-interaction --url=http://127.0.0.1:8000`
- `php artisan capell:extension-install capell-app/wordpress-importer --no-interaction --url=http://127.0.0.1:8000`
- `php artisan route:list | rg 'welcome|Welcome|settings|Settings|translation|Translation' || true`
- `php artisan route:list | rg 'translation|Translation|translations' || true`
- `php artisan route:list | rg 'migration|Migration|import|Import|wordpress|WordPress' || true`
- `php artisan capell:doctor --no-interaction`
- `vendor/bin/pest packages/theme-corporate/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/theme-saas/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/translation-manager/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/welcome-tour/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/wordpress-importer/tests --configuration=phpunit.xml`

## Test Status

- `theme-corporate`: passed, 4 tests, 22 assertions.
- `theme-saas`: passed, 4 tests, 21 assertions.
- `translation-manager`: passed, 18 tests, 44 assertions.
- `welcome-tour`: passed, 15 tests, 33 assertions.
- `wordpress-importer`: passed, 2 tests, 11 assertions.

## Harness Concerns

- Theme package installs are feasible but require installing `capell-app/layout-builder` before `capell-app/foundation-theme`, then the target theme. Composer pulled `capell-app/content-blocks`, `capell-app/layout-builder`, and `capell-app/foundation-theme` for the theme packages.
- `capell:doctor` still reports missing frontend/theme build artifacts after theme installs, especially `public/vendor/capell-foundation-theme/manifest.json`. This means final visual screenshots need a package-specific build/publish pass after installing the Foundation Theme stack.
- Composer requiring `translation-manager`, `welcome-tour`, and `wordpress-importer` downgraded `brick/math` and `ramsey/uuid` in the harness lock file. This stayed isolated to the batch harness.

## theme-corporate

Composer name: `capell-app/theme-corporate`
Hard dependencies: `capell-app/core`, `capell-app/foundation-theme`; install flow also required `capell-app/layout-builder` before Foundation Theme could be marked installed.
Visible surfaces: frontend renderer only. No package-owned routes, models, migrations, settings, admin resources, or admin pages. The visible admin touchpoint is the core Theme resource once Foundation Theme is installed.

Observed surfaces:

| Surface                    | URL                          | Notes                                                                                                                 |
| -------------------------- | ---------------------------- | --------------------------------------------------------------------------------------------------------------------- |
| Core Themes admin resource | `/admin/themes`              | Baseline/core surface where the Corporate theme should become selectable.                                             |
| Public page renderer       | `/theme-corporate-demo`      | Renders navigation, hero, features, proof, content listing, CTA, and footer without public package/theme identifiers. |
| Theme preview output       | `capell.admin.theme-preview` | Signed host preview route for an authenticated administrator.                                                         |

Docs status:

- Existing `README.md`, `docs/overview.md`, and `docs/screenshots.json` already described the renderer-only shape and screenshot plan.
- Issues fixed: public wrapper and section views no longer emit `data-capell-theme` or `capell-theme-*` classes.
- Screenshot manifest entries now point at `ThemeResource:index`, `/theme-corporate-demo`, and `capell.admin.theme-preview`.
- Screenshots captured:
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-corporate/theme-admin-list-showing-corporate.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-corporate/frontend-page-rendered-with-corporate-theme.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-corporate/theme-preview-url-output.png`
- Public safety check: `/theme-corporate-demo` rendered successfully and the HTML body scan found no `capell-theme`, `data-capell-theme`, `theme-corporate`, `signed`, `filament`, `editor`, or `/admin`.

Feature suggestions:

- Add a small Corporate demo content preset that creates a page with all supported section types so screenshots are reproducible.
- Add theme setup health checks for missing Foundation Theme manifests, missing frontend npm dependencies, and stale Theme Studio presets from a different theme.
- Add responsive screenshot entries for mobile navigation and proof/content-listing variants because those are the most theme-specific frontend surfaces.

## theme-saas

Composer name: `capell-app/theme-saas`
Hard dependencies: `capell-app/core`, `capell-app/foundation-theme`; install flow also required `capell-app/layout-builder` before Foundation Theme could be marked installed.
Visible surfaces: frontend renderer only. No package-owned routes, models, migrations, settings, admin resources, or admin pages. The visible admin touchpoint is the core Theme resource once Foundation Theme is installed.

Observed surfaces:

| Surface                    | URL                          | Notes                                                                                                                 |
| -------------------------- | ---------------------------- | --------------------------------------------------------------------------------------------------------------------- |
| Core Themes admin resource | `/admin/themes`              | Baseline/core surface where the SaaS theme should become selectable.                                                  |
| Public page renderer       | `/theme-saas-demo`           | Renders navigation, hero, features, proof, content listing, CTA, and footer without public package/theme identifiers. |
| Theme preview output       | `capell.admin.theme-preview` | Signed host preview route for an authenticated administrator.                                                         |

Docs status:

- Existing `README.md`, `docs/overview.md`, and `docs/screenshots.json` already described the renderer-only shape and screenshot plan.
- Issues fixed: public wrapper and section views no longer emit `data-capell-theme` or `capell-theme-*` classes.
- Screenshot manifest entries now point at `ThemeResource:index`, `/theme-saas-demo`, and `capell.admin.theme-preview`.
- Screenshots captured:
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-saas/theme-admin-list-showing-saas.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-saas/frontend-page-rendered-with-saas-theme.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-5/public/docs/screenshots/packages/theme-saas/theme-preview-url-output.png`
- Public safety check: `/theme-saas-demo` rendered successfully and the HTML body scan found no `capell-theme`, `data-capell-theme`, `theme-saas`, `signed`, `filament`, `editor`, or `/admin`.

Feature suggestions:

- Add a SaaS demo content preset with feature, proof, CTA, and product media data for deterministic screenshot capture.
- Add setup health checks for missing Foundation Theme manifests, missing frontend npm dependencies, and stale Theme Studio presets from a different theme.
- Add screenshot entries for Launch, Platform, and Labs presets so the package value is visible beyond the default renderer.
- Add visual regression fixtures for long CTA labels and long feature titles, since the SaaS renderer uses compact cards and prominent buttons.

## translation-manager

Composer name: `capell-app/translation-manager`
Hard dependencies: `capell-app/admin`, `capell-app/core`
Optional dependencies: `capell-app/ai-orchestrator`
Visible surfaces: intended Filament admin page and action modals. No public frontend surface.

Observed surfaces:

| Surface                           | URL                          | Notes                                                                                               |
| --------------------------------- | ---------------------------- | --------------------------------------------------------------------------------------------------- |
| Translation Manager Filament page | `/admin/translation-manager` | Source/locale/file/filter controls and translation comparison grid are implemented in package code. |
| Create locale modal               | `/admin/translation-manager` | Header action creates locale files from the source locale.                                          |
| Duplicate locale modal            | `/admin/translation-manager` | Header action duplicates an existing locale.                                                        |
| Translate selected action         | `/admin/translation-manager` | Hidden unless an AI translator binding is available.                                                |

Install result:

- `composer require` and `capell:extension-install` succeeded.
- Corrected route verification with `php artisan route:list | rg 'translation|Translation|translations'` showed `GET|HEAD admin/translation-manager`.

Docs status:

- Updated `docs/overview.md` with install audit, surfaces, and screenshot coverage.
- Added `docs/screenshots.json`.
- Screenshot manifest entries now include concrete use cases and deterministic output paths.
- Screenshots captured:
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-translation-manager/public/docs/screenshots/packages/translation-manager/translation-manager-page-empty-state.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-translation-manager/public/docs/screenshots/packages/translation-manager/translation-manager-comparison-grid.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-translation-manager/public/docs/screenshots/packages/translation-manager/translation-manager-create-locale-modal.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-translation-manager/public/docs/screenshots/packages/translation-manager/translation-manager-duplicate-locale-modal.png`
- Harness isolation: removed the baseline `capell-app/login-audit` package and deleted its extension row before capture. Composer package checks showed only the core stack plus `capell-app/translation-manager`.
- Screenshot fixture: the harness used `lang/en/package.php` and a partial `lang/fr/package.php` so the comparison grid shows changed and missing entries.
- Local-source noise: set `capell-translation-manager.package_paths` to `[]` in the disposable harness because the default local path config lists every sibling package repository when the app sits beside `capell-packages-4`.
- Optional AI action: not captured in this pass because no `TranslationAITranslator` binding was available.

Feature suggestions:

- Add an install verification test proving the `TranslationManagerPage` route stays registered after the extension is installed.
- Add an installed-package source mode or setup warning so local demo apps do not list uninstalled sibling package repositories by default.
- Add a read-only package-source preview mode that clearly shows when edits will be written to override paths.
- Add a translation coverage summary by locale and source before the grid so editors can prioritize missing or stale files.
- Add a dry-run diff before saving translations, especially for package override writes.

## welcome-tour

Composer name: `capell-app/welcome-tour`
Hard dependencies: `capell-app/admin`
Visible surfaces: admin dashboard replacement, tour overlay, Settings group, user-resource bridge field when supported. No public frontend surface.

Observed surfaces:

| Surface                     | URL                          | Notes                                                                                |
| --------------------------- | ---------------------------- | ------------------------------------------------------------------------------------ |
| Welcome Tour dashboard      | `/admin`                     | Route registered as `filament.admin.pages.welcome-tour-dashboard`.                   |
| Welcome tour overlay        | `/admin`                     | Provided by `jibaymcs/filament-tour`; visible based on settings and user preference. |
| Welcome tour settings group | `/admin/settings`            | Shared Settings page surface with enable toggle and step repeater.                   |
| User edit toggle            | `/admin/users/{record}/edit` | Bridge contributes `welcome_tour_enabled` only when `users.dismissed_hints` exists.  |

Docs status:

- Updated `docs/overview.md` with install audit, surfaces, and screenshot coverage.
- Added `docs/screenshots.json`.
- Screenshot manifest entries now include concrete use cases and deterministic output paths.
- Issues fixed: Welcome Tour now registers a package settings page at `/admin/extensions/welcome-tour/settings`; previously it registered a settings schema but had no reachable package settings page because the global Settings page is first-party only.
- Screenshots captured:
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-welcome-tour/public/docs/screenshots/packages/welcome-tour/welcome-tour-dashboard.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-welcome-tour/public/docs/screenshots/packages/welcome-tour/welcome-tour-overlay.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-welcome-tour/public/docs/screenshots/packages/welcome-tour/welcome-tour-settings.png`
    - `/Users/ben/Sites/packages/capell/capell-package-demo-audit-welcome-tour/public/docs/screenshots/packages/welcome-tour/welcome-tour-user-toggle.png`
- Harness isolation: removed the baseline `capell-app/login-audit`, `tapp/filament-authentication-log`, and direct `rappasoft/laravel-authentication-log` dependency before capture. The copied app user model also needed its `AuthenticationLoggable` trait removed after the direct dependency was removed.
- Settings setup: published and ran `2026_05_10_190836_01_add_welcome_tour_settings.php` so `welcome-tour.enabled` and `welcome-tour.steps` existed before browser capture.

Feature suggestions:

- Add a one-click "reset tour for this user" action on the settings page or user edit form.
- Add selector validation for configured tour steps so admins can spot stale CSS selectors before enabling the tour.
- Add a preview mode in settings to launch the configured tour without
