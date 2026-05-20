# Package Testing Audit

## admin/frontend layout builder APIs

- [ ] Installs cleanly in a fresh Capell demo workbench
- [ ] Declared package dependencies are correct
- [ ] Migrations run cleanly and are idempotent
- [ ] Service provider boots without duplicate registry keys
- [ ] Filament resources, pages, widgets, settings, actions, and relation managers load
- [ ] Frontend routes, render hooks, widgets, themes, or Blade views work where the package exposes them
- [ ] Custom package functionality works through realistic user flows
- [ ] Permissions, roles, workspace boundaries, and draft/publish behaviour are correct where applicable
- [ ] Caching, queues, scheduled jobs, webhooks, commands, and external integrations degrade safely
- [ ] Package Pest suite is meaningful, not just smoke coverage
- [ ] Coverage gap list exists for untested Actions, Data objects, Enums, services, policies, resources, and frontend render paths
- [ ] `README.md` and package docs match the real install and usage flow
- [ ] Admin/frontend core docs cover the important layout builder admin and frontend surfaces
- [ ] Generated screenshots are clear, seeded with useful data, and not hiding broken UI states
- [ ] Known risks and follow-up issues are recorded

## blog

- [x] Installs cleanly in a fresh Capell demo workbench
- [x] Declared package dependencies are correct
- [x] Migrations run cleanly and are idempotent
- [x] Service provider boots without duplicate registry keys
- [x] Filament resources, pages, widgets, settings, actions, and relation managers load
- [x] Frontend routes, render hooks, widgets, themes, or Blade views work where the package exposes them
- [x] Custom package functionality works through realistic user flows
- [x] Permissions, roles, workspace boundaries, and draft/publish behaviour are correct where applicable
- [x] Caching, queues, scheduled jobs, webhooks, commands, and external integrations degrade safely
- [x] Package Pest suite is meaningful, not just smoke coverage
- [x] Coverage gap list exists for untested Actions, Data objects, Enums, services, policies, resources, and frontend render paths
- [x] `README.md` and package docs match the real install and usage flow
- [x] `packages/blog/docs/screenshots.json` covers the important admin and frontend surfaces
- [x] Generated screenshots are clear, seeded with useful data, and not hiding broken UI states
- [x] Known risks and follow-up issues are recorded
- Blog test suite passed cleanly: Arch, Filament resources, widgets, static-site behavior, and integration command coverage all green.
- Blog dependencies are explicit in `capell.json` and no longer include a separate `capell-app/layout-builder` package.

## address

- [x] Installs cleanly in a fresh Capell demo workbench
- [x] Declared package dependencies are correct
- [x] Migrations run cleanly and are idempotent
- [x] Service provider boots without duplicate registry keys
- [x] Filament resources, pages, widgets, settings, actions, and relation managers load
- [x] Frontend routes, render hooks, widgets, themes, or Blade views work where the package exposes them
- [x] Custom package functionality works through realistic user flows
- [x] Permissions, roles, workspace boundaries, and draft/publish behaviour are correct where applicable
- [x] Caching, queues, scheduled jobs, webhooks, commands, and external integrations degrade safely
- [x] Package Pest suite is meaningful, not just smoke coverage
- [x] Coverage gap list exists for untested Actions, Data objects, Enums, services, policies, resources, and frontend render paths
- [x] `README.md` and package docs match the real install and usage flow
- [x] `packages/address/docs/screenshots.json` covers the important admin and frontend surfaces
- [x] Generated screenshots are clear, seeded with useful data, and not hiding broken UI states
- [x] Known risks and follow-up issues are recorded
- Address test suite passed cleanly with 81 passing tests and 277 assertions, including resource, model, observer, command, and flag-render coverage.
- `packages/address/docs/screenshots.json` is present and should be kept in sync with the country/address admin surfaces.

## ai-orchestrator

- [x] Installs cleanly in a fresh Capell demo workbench
- [x] Declared package dependencies are correct
- [x] Migrations run cleanly and are idempotent
- [x] Service provider boots without duplicate registry keys
- [x] Filament resources, pages, widgets, settings, actions, and relation managers load
- [x] Frontend routes, render hooks, widgets, themes, or Blade views work where the package exposes them
- [x] Custom package functionality works through realistic user flows
- [x] Permissions, roles, workspace boundaries, and draft/publish behaviour are correct where applicable
- [x] Caching, queues, scheduled jobs, webhooks, commands, and external integrations degrade safely
- [x] Package Pest suite is meaningful, not just smoke coverage
- [x] Coverage gap list exists for untested Actions, Data objects, Enums, services, policies, resources, and frontend render paths
- [x] `README.md` and package docs match the real install and usage flow
- [x] `packages/ai-orchestrator/docs/screenshots.json` covers the important admin and frontend surfaces
- [x] Generated screenshots are clear, seeded with useful data, and not hiding broken UI states
- [x] Known risks and follow-up issues are recorded
- AIOrchestrator test suite passed cleanly with boundary, capability, and registry coverage in place.
- The package surface stays isolated from removed `Capell\LayoutBuilder` namespaces, which matches the package boundary rule.

## insights

- [x] Installs cleanly in a fresh Capell demo workbench
- [x] Declared package dependencies are correct
- [x] Migrations run cleanly and are idempotent
- [x] Service provider boots without duplicate registry keys
- [x] Filament resources, pages, widgets, settings, actions, and relation managers load
- [x] Frontend routes, render hooks, widgets, themes, or Blade views work where the package exposes them
- [x] Custom package functionality works through realistic user flows
- [x] Permissions, roles, workspace boundaries, and draft/publish behaviour are correct where applicable
- [x] Caching, queues, scheduled jobs, webhooks, commands, and external integrations degrade safely
- [x] Package Pest suite is meaningful, not just smoke coverage
- [x] Coverage gap list exists for untested Actions, Data objects, Enums, services, policies, resources, and frontend render paths
- [x] `README.md` and package docs match the real install and usage flow
- [x] `packages/insights/docs/screenshots.json` covers the important admin and frontend surfaces
- [x] Generated screenshots are clear, seeded with useful data, and not hiding broken UI states
- [x] Known risks and follow-up issues are recorded
- Insights test suite passed cleanly with 77 tests and 382 assertions, including consent, beacon, frontend script, settings, provider, retention, and widget coverage.
- Screenshot manifest is present for the core admin and frontend surfaces called out in the README.

## current audit notes

- `deployments` now has a Capell manifest and screenshot manifest, so it should be included in the normal manifest-backed package audit flow.
- `agent-bridge` has `docs/screenshots.json`, but its manifest entries still need composer name cleanup where `composerName` is null.
- `packages/foundation-theme/` is the single canonical `capell-app/foundation-theme` package after consolidating the old compatibility resources.
- `frontend-authoring` is using `capell-app/frontend-authoring` in screenshot metadata, which is worth keeping consistent while the manifest is audited.

## 2026-05-19 isolated package demo audit

The package demo audit harness was created at `/Users/ben/Sites/packages/capell/capell-package-demo-audit` and documented in [package-demo-audit-harness.md](package-demo-audit-harness.md). The core baseline is documented in [core-demo-baseline.md](internal/core-demo-baseline.md), with structured admin baseline data in [core-admin-menu-baseline.json](internal/core-admin-menu-baseline.json).

All 45 package directories now have `docs/screenshots.json`, and the aggregate screenshot manifest regenerates successfully:

```bash
npm run screenshots:manifest
npm run screenshots:validate
```

Validation result: `Screenshot manifests are in sync.`

Detailed package notes and feature suggestions are stored in:

- [batch-1.md](internal/package-audits/batch-1.md): `access-gate`, `address`, `agent-bridge`, `ai-orchestrator`, `api`, `block-library`, `blog`, `campaign-studio`
- [batch-2.md](internal/package-audits/batch-2.md): `content-sections`, `dashboard-reports`, `demo-kit`, `deployments`, `diagnostics`, `document-lifecycle`, `email-studio`, `events`
- [batch-3.md](internal/package-audits/batch-3.md): `form-builder`, `foundation-theme`, `frontend-authoring`, `frontend-optimizer`, `ga4-reports`, `hero`, `html-cache`, `insights`
- [batch-4.md](internal/package-audits/batch-4.md): `layout-builder`, `login-audit`, `media-ai`, `media-library`, `migration-assistant`, `navigation`, `newsletter`, `notes`
- [batch-5.md](internal/package-audits/batch-5.md): `password-policy`, `public-actions`, `publishing-studio`, `search`, `seo-suite`, `site-discovery`, `tags`, `theme-agency`
- [batch-6.md](internal/package-audits/batch-6.md): `theme-corporate`, `theme-saas`, `translation-manager`, `welcome-tour`, `wordpress-importer`

### Audit status

| Area                     | Status                                                                                                                                                              |
| ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Package inventory        | Complete: 45 package directories audited                                                                                                                            |
| Screenshot contracts     | Complete: 45 package manifests, 229 aggregate entries                                                                                                               |
| Package tests            | Complete: focused package Pest suites passed for all audited packages                                                                                               |
| Isolated install checks  | Complete for `login-audit` after adding the Laravel 13 Rappasoft fork alias; remaining package results stay as recorded in batch notes                              |
| Final visual screenshots | In progress: `login-audit` admin screenshots are captured in the shared Laravel 13 harness; frontend/theme package screenshots still require a theme asset baseline |

### Blockers and risks

- `login-audit` now installs in the Laravel 13 harness when the host app requires `rappasoft/laravel-authentication-log` from the `fdemb` PR #140 fork as `dev-main as 6.0.1`. Keep this root-app alias until upstream releases Laravel 13 support.
- The baseline harness now publishes `public/vendor/capell-frontend/manifest.json` through `php artisan capell:frontend-install --no-interaction --ansi`. It still reports missing generated Capell frontend Tailwind CSS because the core baseline is installed with `--theme=none`; the generator binding is provided by `capell-app/foundation-theme`.
- Dependency-heavy packages such as `blog`, `campaign-studio`, `foundation-theme`, `hero`, and theme packages require hard dependencies to be installed as Capell extensions, not just Composer packages, before the target extension is marked installed.
- Some Composer remove cycles in isolated harnesses logged pre-uninstall event handler errors but still completed; keep clearing `bootstrap/cache/packages.php` and `bootstrap/cache/services.php` between package cycles.
- This pass mapped required screenshots and use cases. The Login Audit screenshots are captured; the remaining packages still need the same image-backed pass.
