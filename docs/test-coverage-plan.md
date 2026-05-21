# Package Coverage Plan

This plan is for raising `capell-packages-4` to at least 80% repository coverage while keeping the work package-owned and useful. The current local full coverage run completes with all tests passing, but reports 64.8% total coverage. The single CI coverage authority is `.github/workflows/coverage-release.yml`.

## Rules

- Keep one coverage workflow. Do not add package-specific coverage workflows or Composer aliases that drift from CI.
- Keep the release coverage workflow strict: `vendor/bin/pest --coverage --min=80 --coverage-clover=coverage/clover.xml`.
- Add tests around meaningful behavior, not accessor noise.
- Prefer Action tests via `Action::run()` for domain behavior.
- Use integration tests only where a package boundary, service provider, render hook, migration, or Filament registration is the behavior under test.
- Avoid snapshot tests unless the serialized output is intentionally stable.
- Do not lower coverage thresholds to make a branch green. Raise coverage or narrow `phpunit.xml` source exclusions only when the excluded code is genuinely non-behavioral bootstrapping.

## Measurement

1. Run the single coverage command from the workflow locally:

    ```bash
    php -d memory_limit=-1 -d pcov.enabled=1 -d pcov.directory=packages -d pcov.exclude="~vendor|tests|storage|bootstrap|.temp~" vendor/bin/pest --coverage --min=80 --coverage-clover=coverage/clover.xml --colors=always --stop-on-error --stop-on-failure --configuration=phpunit.xml
    ```

2. Use the Clover output to generate a package rollup before each coverage sprint. Group files by `packages/{package}/src`.
3. Track each package with: current line coverage, uncovered Actions, uncovered providers/registrars, uncovered public render paths, and the next three tests to write.
4. Ratchet package floors after each batch. A package that reaches 80% should not fall below 80% again.

## Priority Order

### 1. High-impact low-coverage packages

Start with packages that have many source files and broad user-facing behavior:

- `seo-suite`
- `layout-builder`
- `blog`
- `publishing-studio`
- `migration-assistant`
- `access-gate`
- `agent-bridge`
- `campaign-studio`

For each package:

- Cover every Action with at least one happy path and one failure or edge path.
- Cover data builders and report builders with real model factories where possible.
- Cover render hooks and public output for anonymous safety where frontend output is involved.
- Cover settings schemas and package boot registration with focused integration tests.

### 2. Admin and Filament-heavy packages

Next, cover packages where regressions mostly show up in admin resources, settings, or widgets:

- `diagnostics`
- `dashboard-reports`
- `deployments`
- `document-lifecycle`
- `email-studio`
- `events`
- `newsletter`
- `translation-manager`
- `welcome-tour`

For each package:

- Test query/build Actions directly.
- Test Filament resource schema/table factories by asserting fields, actions, filters, and validation rules.
- Add one smoke test that boots the package provider and verifies key resources or pages are registered.

### 3. Frontend and rendering packages

Then cover packages whose risk is public HTML, render hooks, page cache safety, or frontend state:

- `foundation-theme`
- `frontend-authoring`
- `frontend-optimizer`
- `hero`
- `html-cache`
- `insights`
- `navigation`
- `public-actions`
- `search`
- `site-discovery`
- `theme-agency`
- `theme-corporate`
- `theme-saas`

For each package:

- Assert anonymous and non-admin responses expose no editor or authoring surface.
- Test render-hook registration with fresh registry instances to catch order-dependent bugs.
- Test Blade render paths with hydrated data only; public views must not perform queries.
- Test cache keys, invalidation decisions, and static-safe output.

### 4. Small and utility packages

Finish with the small packages, which should be quick to bring above 80%:

- `address`
- `ai-orchestrator`
- `api`
- `block-library`
- `content-sections`
- `demo-kit`
- `form-builder`
- `ga4-reports`
- `login-audit`
- `media-ai`
- `media-library`
- `notes`
- `password-policy`
- `tags`
- `wordpress-importer`

For each package:

- Add one provider/registration test.
- Add direct tests for all Actions and support services.
- Add model scope or enum label tests only when those methods contain actual behavior.

## Execution Loop

For each package batch:

1. Generate the package coverage rollup from `coverage/clover.xml`.
2. Pick the lowest uncovered behavior with the highest production risk.
3. Add tests in small groups, usually one Action or one support class at a time.
4. Run the narrow package tests:

    ```bash
    vendor/bin/pest packages/{package}/tests --configuration=phpunit.xml
    ```

5. Run the workflow coverage command after each package reaches its target.
6. Update this plan with the new package percentage and remaining gaps.

## Progress Log

### `frontend-authoring`

- Added direct coverage for `BuildEditableRegionManifestAction`, including default editable regions, signed edit URLs, selector configuration, and package-supplied region extenders.
- Verified the package suite with `vendor/bin/pest packages/frontend-authoring/tests --configuration=phpunit.xml` (27 tests, 188 assertions).
- Remaining gap: generate the next `coverage/clover.xml` package rollup and record the exact package percentage.

## Acceptance Criteria

- The single coverage workflow is the only CI coverage entry point.
- The workflow fails below 80% total coverage.
- `coverage/clover.xml` is uploaded by the workflow for reporting.
- Every package has either at least 80% package coverage or a documented short-term gap with the exact uncovered files and the next tests planned.
- Repository coverage reaches at least 80% with all tests passing.
