# Frontend Optimizer Overview

Frontend Optimizer records render profiles for public Capell pages and renders profile-aware CSS and JavaScript assets through the `@frontendOptimizerAssets(...)` Blade directive.

Use it when a site needs deterministic asset delivery per layout, widget set, site, locale, or theme context without hard-coding frontend bundles into theme views.

## What It Adds

- `@frontendOptimizerAssets(...)` for rendering the resolved asset profile.
- Layout and widget asset registries for package-owned frontend assets.
- Critical CSS generation through `CriticalCssGenerator`, backed by Playwright by default.
- Render profile actions for resolving, preparing, persisting, and storing profile manifests.
- `frontend_render_profiles` and `frontend_optimization_runs` tables.

## Install Impact

- Requires `capell-app/core` and `capell-app/frontend`.
- Adds one migration file that creates render profile and optimization run storage.
- Adds no Filament pages, admin resources, public routes, or settings screen.
- Frontend output is visible only where a theme or frontend view calls the Blade directive.

## Admin Surfaces

None. This package has no `src/Filament` classes and registers no admin surface.

## Frontend Surfaces

| Surface                   | Use case                                                                               | Screenshot                               |
| ------------------------- | -------------------------------------------------------------------------------------- | ---------------------------------------- |
| Blade directive output    | Verify that the active render profile emits the expected asset tags for a public page. | `frontend-optimizer-profile-assets`      |
| Critical CSS run artifact | Verify generated critical CSS and manifest metadata for a seeded page profile.         | `frontend-optimizer-critical-css-output` |

## Demo Setup

Install the core baseline plus `capell-app/frontend-optimizer`, run migrations through the host app, seed or render at least one public page, then capture the public page HTML around the `@frontendOptimizerAssets(...)` output. A useful screenshot requires a theme fixture that actually calls the directive.

## Screenshot Coverage

The package needs screenshot coverage for asset output, not for admin navigation. Captures should include the public page and enough response/source inspection to prove the expected profile assets were emitted.

## Known Risks

- Empty screenshots are likely unless the demo theme calls the directive.
- Playwright-backed critical CSS generation depends on the host runtime and browser availability.
- Asset profile keys vary by render context; demo data should state the site, locale, layout, and theme used for capture.

## Verification

Run package tests from the repository root:

```bash
vendor/bin/pest packages/frontend-optimizer/tests --configuration=phpunit.xml
```
