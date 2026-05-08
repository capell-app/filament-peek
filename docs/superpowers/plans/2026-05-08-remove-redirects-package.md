# Remove Redirects Package Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove the standalone `capell-app/redirects` package and move its runtime behavior into first-party Capell core behavior without losing redirect resolution, automatic redirects, hit tracking, or health snapshots.

**Architecture:** Treat this as a reverse of the May 2026 redirects package extraction. Core should own redirect domain/runtime code because redirects are stored in `page_urls`; admin keeps the existing redirect resource; frontend keeps the public frontend `RedirectResolver` contract and delegates to the core implementation. The separate `Capell\Redirects` namespace, manifest, service provider, package install gates, and package test bootstrap should disappear.

**Tech Stack:** PHP 8.2, Laravel 11/12/13, Filament 4/5, Pest, Laravel Actions, Spatie Laravel Data, Capell core/admin/frontend packages.

---

## File Structure

- Move to core: `packages/redirects/src/Actions/AddRedirectUrlAction.php` -> `../capell-4/packages/core/src/Actions/Redirects/AddRedirectUrlAction.php`.
- Move to core: `packages/redirects/src/Actions/CreateAutomaticRedirectAction.php` -> `../capell-4/packages/core/src/Actions/Redirects/CreateAutomaticRedirectAction.php`.
- Move to core: `packages/redirects/src/Actions/RefreshRedirectHealthSnapshotAction.php` -> `../capell-4/packages/core/src/Actions/Redirects/RefreshRedirectHealthSnapshotAction.php`.
- Move to core: `packages/redirects/src/Actions/RefreshRedirectHealthSnapshotsAction.php` -> `../capell-4/packages/core/src/Actions/Redirects/RefreshRedirectHealthSnapshotsAction.php`.
- Move to core: `packages/redirects/src/Listeners/CreateRedirectsForChangedPageUrls.php` -> `../capell-4/packages/core/src/Listeners/CreateRedirectsForChangedPageUrls.php`.
- Move to core: `packages/redirects/src/Models/RedirectHealthSnapshot.php` -> `../capell-4/packages/core/src/Models/RedirectHealthSnapshot.php`.
- Move to core: `packages/redirects/src/Support/PageUrlRedirectResolver.php` -> `../capell-4/packages/core/src/Support/Redirects/PageUrlRedirectResolver.php`.
- Move to core: `packages/redirects/src/Support/PageUrlRedirectRecorder.php` -> `../capell-4/packages/core/src/Support/Redirects/PageUrlRedirectRecorder.php`.
- Remove duplicate package-only bridge/data/contracts: `packages/redirects/src/Data/RedirectDecisionData.php`, `packages/redirects/src/Contracts/*`, `packages/redirects/src/Support/FrontendRedirectResolver.php`, `packages/redirects/src/Support/RedirectsPackageUrlRecorder.php`.
- Move migrations into core: `packages/redirects/database/migrations/create_redirect_health_snapshots_table.php` and `packages/redirects/database/migrations/2026_05_03_000001_add_page_url_hit_columns.php` -> `../capell-4/packages/core/database/migrations/`.
- Move config into core config namespace: `packages/redirects/config/redirects.php` -> `../capell-4/packages/core/config/redirects.php`, registered by `CapellServiceProvider`.
- Move package tests into core tests under `../capell-4/packages/core/tests/Integration/Redirects` and `../capell-4/packages/core/tests/Unit/Redirects`.
- Update admin references from `Capell\Redirects\Models\RedirectHealthSnapshot` to `Capell\Core\Models\RedirectHealthSnapshot`.
- Update package-repo test bootstrap and package registry tests to remove Redirects as an optional package.
- Remove package files and metadata: `packages/redirects`, root Composer PSR-4 entries, `tests/Pest.php` redirect group, package docs/manifest references.

## Scope Decisions

- Keep redirect storage in `page_urls`; no new `redirects` table.
- Keep `Capell\Frontend\Contracts\RedirectResolver` and `Capell\Frontend\Data\RedirectDecisionData`; core binds that frontend contract only when the frontend package is present.
- Keep the redirect admin UI in `capell-app/admin`; it already lives there.
- Remove `capell-app/redirects` from install/uninstall semantics. Redirects become always available core capability.
- Keep translation keys in existing core/admin namespaces where possible. Replace `__('redirects::...')` in moved code with `__('capell-core::...')` for core behavior.
- Do not add compatibility aliases for `Capell\Redirects\*` unless a downstream package outside this monorepo is known to require them. The goal is package removal.

## Task 1: Baseline And Reference Map

**Files:**

- Read: `packages/redirects/src`
- Read: `packages/redirects/tests`
- Read: `../capell-4/packages/core/src/Providers/CapellServiceProvider.php`
- Read: `../capell-4/packages/admin/src/Providers/AdminServiceProvider.php`
- Read: `../capell-4/packages/frontend/src/Support/Loader/PageResolver.php`

- [ ] **Step 1: Confirm dirty trees**

Run:

```bash
git -C /Users/ben/Sites/packages/capell/capell-packages-4 status --short
git -C /Users/ben/Sites/packages/capell/capell-4 status --short
```

Expected: record unrelated existing changes and avoid reverting or formatting them.

- [ ] **Step 2: Capture Redirects namespace references**

Run:

```bash
rg -n -F 'Capell\Redirects' /Users/ben/Sites/packages/capell/capell-packages-4 /Users/ben/Sites/packages/capell/capell-4
```

Expected: references in `packages/redirects`, package test bootstraps, uninstalled-package tests, and admin redirect health lookup. Use this as the deletion checklist.

- [ ] **Step 3: Capture package identity references**

Run:

```bash
rg -n 'capell-app/redirects|packages/redirects|RedirectsServiceProvider|redirects/capell\.json|group\('\''redirects'\''' /Users/ben/Sites/packages/capell/capell-packages-4 /Users/ben/Sites/packages/capell/capell-4
```

Expected: references in Composer autoload, `tests/Pest.php`, package installation tests, docs/product manifests, and boost/package resources.

- [ ] **Step 4: Run focused baseline tests**

Run:

```bash
cd /Users/ben/Sites/packages/capell/capell-packages-4
vendor/bin/pest packages/redirects/tests --configuration=phpunit.xml
```

Expected: current redirect package behavior passes before moving code. If it fails, capture the failure and continue only after deciding whether it is pre-existing.

## Task 2: Move Redirect Runtime Into Core

**Files:**

- Create: `../capell-4/packages/core/src/Actions/Redirects/AddRedirectUrlAction.php`
- Create: `../capell-4/packages/core/src/Actions/Redirects/CreateAutomaticRedirectAction.php`
- Create: `../capell-4/packages/core/src/Support/Redirects/PageUrlRedirectResolver.php`
- Create: `../capell-4/packages/core/src/Support/Redirects/PageUrlRedirectRecorder.php`
- Modify: `../capell-4/packages/core/src/Support/Redirects/PageUrlRedirectUrlRecorder.php`

- [ ] **Step 1: Move `AddRedirectUrlAction`**

Copy the behavior from `packages/redirects/src/Actions/AddRedirectUrlAction.php` to `Capell\Core\Actions\Redirects\AddRedirectUrlAction`. Preserve the `Pageable`, `Language`, URL validation, site-language validation, redirect creation, and `deleteCache()` behavior. Keep the URL-safe regex as-is for behavior parity.

- [ ] **Step 2: Move `CreateAutomaticRedirectAction`**

Copy the behavior from `packages/redirects/src/Actions/CreateAutomaticRedirectAction.php` to `Capell\Core\Actions\Redirects\CreateAutomaticRedirectAction`. Change config reads from `redirects.auto_redirects.*` only if Task 5 changes the config key; otherwise keep `redirects.auto_redirects.*` for backwards-compatible app config.

- [ ] **Step 3: Move hit recording**

Copy `PageUrlRedirectRecorder` to `Capell\Core\Support\Redirects\PageUrlRedirectRecorder`. Keep the atomic `DB::raw('hit_count + 1')` update and `CarbonImmutable::now()`.

- [ ] **Step 4: Move redirect resolution**

Copy `PageUrlRedirectResolver` to `Capell\Core\Support\Redirects\PageUrlRedirectResolver`, but make it implement `Capell\Frontend\Contracts\RedirectResolver` and return `Capell\Frontend\Data\RedirectDecisionData`. Do not keep the package-local `RedirectDecisionData` DTO.

- [ ] **Step 5: Simplify `PageUrlRedirectUrlRecorder`**

Change `Capell\Core\Support\Redirects\PageUrlRedirectUrlRecorder::record()` to delegate to `AddRedirectUrlAction::run($pageable, $language, $url)` only if the stricter source validation and duplicate behavior are acceptable. If exact current admin behavior is required, leave the existing implementation and only add tests proving it still creates active 301 page URL redirects with `target_url`.

- [ ] **Step 6: Verify core action tests fail until tests are moved**

Run:

```bash
cd /Users/ben/Sites/packages/capell/capell-4
vendor/bin/pest packages/core/tests --filter='Redirect' --configuration=phpunit.xml
```

Expected: either no matching tests yet or existing core redirect validation tests pass. New moved tests are added in Task 4.

## Task 3: Register Core Redirect Behavior

**Files:**

- Modify: `../capell-4/packages/core/src/Providers/CapellServiceProvider.php`
- Modify: `../capell-4/packages/frontend/src/Providers/FrontendServiceProvider.php` if frontend currently owns the null binding
- Read: `../capell-4/packages/frontend/src/Support/Loader/NullRedirectResolver.php`

- [ ] **Step 1: Register redirect config**

Update `CapellServiceProvider::configurePackage()` to include the redirects config file:

```php
->hasConfigFile(['capell', 'capell-installer', 'redirects'])
```

- [ ] **Step 2: Bind the frontend redirect resolver when frontend is installed**

In `CapellServiceProvider::packageRegistered()`, add a private registration method that checks `interface_exists(\Capell\Frontend\Contracts\RedirectResolver::class)` before binding:

```php
if (interface_exists(\Capell\Frontend\Contracts\RedirectResolver::class)) {
    $this->app->singleton(
        \Capell\Frontend\Contracts\RedirectResolver::class,
        \Capell\Core\Support\Redirects\PageUrlRedirectResolver::class,
    );
}
```

Expected: core does not hard-require frontend at Composer level, but host apps with frontend get real redirect resolution.

- [ ] **Step 3: Bind hit recorder**

Bind `Capell\Core\Support\Redirects\PageUrlRedirectRecorder` as a concrete singleton if constructor injection is used by `PageUrlRedirectResolver`. Avoid a public core contract unless another caller needs it.

- [ ] **Step 4: Register automatic redirect listener**

Register `Event::listen(PageSaved::class, [CreateRedirectsForChangedPageUrls::class, 'handle']);` from core. Keep the config gate so host apps can disable automatic redirects.

- [ ] **Step 5: Check frontend null binding order**

If `FrontendServiceProvider` binds `RedirectResolver` to `NullRedirectResolver`, make it use `singletonIf()` so the core binding wins when both packages are loaded. If it already uses `singletonIf()`, leave it.

## Task 4: Move Health Snapshot Model, Migrations, And Tests

**Files:**

- Create: `../capell-4/packages/core/src/Models/RedirectHealthSnapshot.php`
- Create: `../capell-4/packages/core/database/migrations/create_redirect_health_snapshots_table.php`
- Create: `../capell-4/packages/core/database/migrations/2026_05_03_000001_add_page_url_hit_columns.php`
- Create/modify: `../capell-4/packages/core/tests/Integration/Redirects/*`
- Modify: `../capell-4/packages/core/src/Models/PageUrl.php`

- [ ] **Step 1: Move `RedirectHealthSnapshot`**

Move the model to `Capell\Core\Models\RedirectHealthSnapshot`. Add PHPDoc properties for `page_url_id`, `source_url`, `target_url`, `warning_count`, `error_count`, and `computed_at` while preserving casts and the `pageUrl()` relation.

- [ ] **Step 2: Move health actions**

Move `RefreshRedirectHealthSnapshotAction` and `RefreshRedirectHealthSnapshotsAction` into `Capell\Core\Actions\Redirects`. Replace `__('redirects::message.redirect_loop_detected')` and `__('redirects::message.redirect_chain_detected')` with core translation keys. Use the existing `ValidateRedirectAction`.

- [ ] **Step 3: Move migrations**

Move both migrations into core unchanged except for filename ordering if needed. Keep the `Schema::hasTable()` and `Schema::hasColumn()` guards.

- [ ] **Step 4: Update `PageUrl` PHPDoc**

Add `hit_count` and `last_hit_at` properties to `PageUrl` PHPDoc and casts if needed. `last_hit_at` should be cast to `datetime` if code or tables read it as a date.

- [ ] **Step 5: Move tests**

Move package tests to core and change namespaces/imports:

```text
packages/redirects/tests/Integration/Actions/AddRedirectUrlActionTest.php
packages/redirects/tests/Integration/Actions/CreateAutomaticRedirectActionTest.php
packages/redirects/tests/Integration/Actions/RedirectHealthSnapshotActionTest.php
packages/redirects/tests/Integration/Listeners/CreateRedirectsForChangedPageUrlsTest.php
packages/redirects/tests/Unit/Support/PageUrlRedirectResolverTest.php
```

Expected imports use `Capell\Core\Actions\Redirects\*`, `Capell\Core\Models\RedirectHealthSnapshot`, and `Capell\Core\Support\Redirects\PageUrlRedirectResolver`.

- [ ] **Step 6: Run moved core tests**

Run:

```bash
cd /Users/ben/Sites/packages/capell/capell-4
vendor/bin/pest packages/core/tests/Integration/Redirects packages/core/tests/Unit/Redirects --configuration=phpunit.xml
```

Expected: all moved redirect behavior passes in core.

## Task 5: Update Admin And Frontend Integration Tests

**Files:**

- Modify: `../capell-4/packages/admin/src/Filament/Resources/Redirects/Tables/RedirectsTable.php`
- Modify: `../capell-4/packages/admin/tests/Feature/Filament/Resources/AdminResourceSiteScopeTest.php`
- Modify: `../capell-4/packages/frontend/tests/Feature/PageResolverTest.php`
- Modify: `tests/Packages/Integration/FilamentPackageNavigationTest.php`

- [ ] **Step 1: Point admin health lookup to core**

Change the dynamic class string in `RedirectsTable::redirectHealthFor()` from `Capell\\Redirects\\Models\\RedirectHealthSnapshot` to `Capell\\Core\\Models\\RedirectHealthSnapshot`. Since redirects are now core, remove the optional `class_exists()` branch if tests show it is no longer needed.

- [ ] **Step 2: Remove skipped redirect admin scope logic**

In admin tests, remove helpers that skip when `RedirectResource` is missing. Redirect admin resource should be treated as always present when admin is installed.

- [ ] **Step 3: Assert frontend uses core redirect resolver**

Update frontend redirect tests to prove a `PageUrl` with `type = redirect` still throws `RedirectException` with the expected target and status code, and that `hit_count` increments.

- [ ] **Step 4: Update package navigation expectations**

In package repo navigation tests, remove `capell-app/redirects` as a package-installed prerequisite. Keep the redirect navigation assertion if admin still contributes `RedirectResource`.

- [ ] **Step 5: Run admin/frontend checks**

Run:

```bash
cd /Users/ben/Sites/packages/capell/capell-4
vendor/bin/pest packages/admin/tests/Feature/Filament/Resources/Redirect packages/frontend/tests/Feature/PageResolverTest.php --configuration=phpunit.xml
```

Expected: redirect admin and frontend redirect behavior pass without the redirects package.

## Task 6: Remove Package Repo Wiring

**Files:**

- Modify: `composer.json`
- Modify: `composer.local.json`
- Modify: `tests/Pest.php`
- Modify: `tests/Packages/PackagesTestCase.php`
- Modify: `tests/Packages/UninstalledPackagesTestCase.php`
- Modify: `tests/Packages/Support/ForcePackagesUninstalledServiceProvider.php`
- Modify: `tests/UninstalledPackages/Integration/UninstalledPackageServiceProviderTest.php`
- Delete: `packages/redirects`

- [ ] **Step 1: Remove Composer autoload entries**

Delete these PSR-4 entries from both root Composer files:

```json
"Capell\\Redirects\\": "packages/redirects/src"
"Capell\\Redirects\\Tests\\": "packages/redirects/tests"
```

- [ ] **Step 2: Remove Pest bootstrap group**

Delete the `RedirectsTestCase` import and the redirects `pest()->extend(...)->group('redirects')` line from `tests/Pest.php`.

- [ ] **Step 3: Remove optional package provider references**

Delete `RedirectsServiceProvider` imports and list entries from package test cases and force-uninstalled helpers. Do not replace them with a new core provider; `CapellServiceProvider` is already loaded through the base test stack.

- [ ] **Step 4: Update uninstalled package assertions**

Remove assertions that `Capell\Redirects\Contracts\RedirectResolver` is unbound. Redirects are no longer optional, so these tests should only assert optional package services.

- [ ] **Step 5: Delete package directory**

Delete `packages/redirects` after all moved files/tests are passing from their new homes.

- [ ] **Step 6: Regenerate autoload**

Run:

```bash
cd /Users/ben/Sites/packages/capell/capell-packages-4
COMPOSER=composer.local.json composer dump-autoload --no-scripts
composer dump-autoload --no-scripts
```

Expected: Composer autoload completes without `Capell\Redirects` references.

## Task 7: Update Docs, Manifests, And Product Metadata

**Files:**

- Modify: `CONTEXT-MAP.md`
- Modify: `README.md`
- Modify: `docs/README.md`
- Modify: `docs/product-groups.md`
- Modify: `docs/package-screenshot-manifest.json`
- Modify: `tests/Packages/Arch/ProductGroupManifestTest.php`
- Modify: `../capell-4/packages/admin/phpstan/optional-packages.stub`
- Modify: `packages/agent-bridge/src/Tools/Knowledge/RecommendPackagesTool.php`
- Modify: `packages/agent-bridge/src/Tools/Site/InspectSiteStateTool.php`

- [ ] **Step 1: Remove Redirects package docs**

Remove Redirects from package lists, product groups, screenshot manifests, and package recommendation examples. Redirects should be described as core/admin capability where relevant, not an installable add-on.

- [ ] **Step 2: Update arch fixtures**

Remove `redirects/capell.json` from manifest/product-group arch expectations.

- [ ] **Step 3: Remove admin PHPStan optional stub**

Delete the `Capell\Redirects\Providers\RedirectsServiceProvider` stub from `../capell-4/packages/admin/phpstan/optional-packages.stub`.

- [ ] **Step 4: Update agent bridge references**

Replace references to optional redirects package/model with core/admin redirect capability. `InspectSiteStateTool` should count `Capell\Core\Models\PageUrl` rows with `type = redirect`, not a nonexistent `Capell\Redirects\Models\Redirect`.

## Task 8: Search Cleanup And Verification

**Files:**

- All touched files

- [ ] **Step 1: Ensure namespace/package removal is complete**

Run:

```bash
rg -n -F 'Capell\Redirects' /Users/ben/Sites/packages/capell/capell-packages-4 /Users/ben/Sites/packages/capell/capell-4
rg -n 'capell-app/redirects|packages/redirects|RedirectsServiceProvider|redirects/capell\.json' /Users/ben/Sites/packages/capell/capell-packages-4 /Users/ben/Sites/packages/capell/capell-4
```

Expected: no live references. Historical docs may remain only if Ben explicitly wants to keep old plan/spec archives.

- [ ] **Step 2: Run focused package repo checks**

Run:

```bash
cd /Users/ben/Sites/packages/capell/capell-packages-4
vendor/bin/pest tests/Packages tests/UninstalledPackages packages/seo-suite/tests/Integration/Actions/CreateRedirectForBrokenLinkActionTest.php packages/seo-suite/tests/Integration/Actions/BuildRedirectOpportunityReportActionTest.php --configuration=phpunit.xml
```

Expected: package registry/uninstalled package tests and SEO redirect integrations pass without the redirects package.

- [ ] **Step 3: Run focused core app checks**

Run:

```bash
cd /Users/ben/Sites/packages/capell/capell-4
vendor/bin/pest packages/core/tests packages/admin/tests/Feature/Filament/Resources/Redirect packages/frontend/tests/Feature/PageResolverTest.php --configuration=phpunit.xml
```

Expected: core, admin redirect resource, and frontend redirect behavior pass.

- [ ] **Step 4: Run final changed-file preflight**

Run:

```bash
cd /Users/ben/Sites/packages/capell/capell-packages-4
composer preflight
```

Expected: changed-file formatting and PHPStan checks pass. If sibling `capell-4` changes are not covered by this command, run its equivalent preflight from `/Users/ben/Sites/packages/capell/capell-4`.

## Out Of Scope

- Creating a dedicated redirects table.
- Regex, wildcard, or rule-based redirects.
- Compatibility shims for `Capell\Redirects\*` classes.
- Moving the existing admin `RedirectResource` into core.
- Reworking SEO Suite redirect opportunity logic beyond namespace/package install assumptions.
