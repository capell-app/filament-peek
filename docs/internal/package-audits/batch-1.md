# Batch 1 Package Demo Audit

Date: 2026-05-19
Worker scope: `access-gate`, `address`, `agent-bridge`, `ai-orchestrator`, `api`, `block-library`, `blog`, `campaign-studio`
Batch harness: `/Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-1`

## Summary

This was a first isolated audit pass using static package inspection plus focused package tests and targeted install attempts in the batch harness. Package source was not edited. Low-risk documentation fixes were made where package surfaces or screenshot contracts were clearly incomplete or wrong.

The main install concern is that Composer package discovery succeeds, but `capell:extension-install` only marks packages installable when hard dependencies are already installed as Capell extensions. This blocked full isolated install verification for `blog` and `campaign-studio`, even though Composer installed their hard dependency packages.

## Commands Run

- `sed -n '1,240p' docs/package-demo-audit-plan.md`
- `sed -n '1,260p' docs/package-demo-audit-harness.md`
- `sed -n '1,220p' docs/internal/core-demo-baseline.md`
- `rg --files packages/{access-gate,address,agent-bridge,ai-orchestrator,api,block-library,blog,campaign-studio}`
- `find packages/{access-gate,address,agent-bridge,ai-orchestrator,api,block-library,blog,campaign-studio}/src/Filament -type f`
- `php -r 'json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);' .../docs/screenshots.json`
- `cp -a /Users/ben/Sites/packages/capell/capell-package-demo-audit /Users/ben/Sites/packages/capell/capell-package-demo-audit-batch-1`
- In batch harness: `composer require ... -W --no-interaction`, `composer remove ... -W --no-interaction`, `php artisan migrate --graceful --ansi`, `php artisan capell:extension-install ... --no-interaction --ansi`, `php artisan route:list --json`
- `vendor/bin/pest packages/access-gate/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/address/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/agent-bridge/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/ai-orchestrator/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/api/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/block-library/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/blog/tests --configuration=phpunit.xml`
- `vendor/bin/pest packages/campaign-studio/tests --configuration=phpunit.xml`

## Test Status

| Package                            | Package tests                     |
| ---------------------------------- | --------------------------------- |
| `access-gate`                      | Passed: 94 tests, 336 assertions  |
| `address`                          | Passed: 88 tests, 301 assertions  |
| `agent-bridge`                     | Passed: 44 tests, 130 assertions  |
| `ai-orchestrator`                  | Passed: 5 tests, 8 assertions     |
| `api`                              | Passed: 17 tests, 70 assertions   |
| `block-library` / `content-blocks` | Passed: 26 tests, 85 assertions   |
| `blog`                             | Passed: 124 tests, 635 assertions |
| `campaign-studio`                  | Passed: 21 tests, 77 assertions   |

## Access Gate

Composer name: `capell-app/access-gate`
Hard dependencies: `capell-app/core`
Optional dependencies: `capell-app/public-actions`
Surfaces: admin, frontend routes, middleware, console, database

Observed surfaces:

- Admin resources: `AccessAreaResource`, `RegistrationResource`, `GrantResource`, `ClaimTokenResource`, `BrowserTokenResource`, `AccessGateEventResource`.
- Admin pages: access area index/create/edit; index pages for registrations, grants, claim tokens, browser tokens, and events.
- Frontend routes: `capell-access-gate.request`, `capell-access-gate.request.store`, `capell-access-gate.claim`, `capell-access-gate.logout`, optional `capell-access-gate.status`.
- Frontend views: request form, gated message, request CTA component.

Install verification:

- Composer require succeeded in the batch harness and package discovery listed `capell-app/access-gate`.
- `php artisan route:list --name=capell-access-gate --json` showed request, store, claim, and logout routes.
- `php artisan migrate --graceful` reported `Nothing to migrate` before extension install, despite schema-owning migrations. Follow up on publish/install sequencing.

Docs updated:

- Added `packages/access-gate/docs/overview.md`.
- Added `packages/access-gate/docs/screenshots.json`.
- Linked overview and screenshot contract from `packages/access-gate/README.md`.

Feature suggestions:

| Suggestion               | User value                                                                              | Implementation risk | Package fit                                    |
| ------------------------ | --------------------------------------------------------------------------------------- | ------------------- | ---------------------------------------------- |
| Access Area health panel | Gives admins a quick view of request URL, middleware, status endpoint, and policy state | Medium              | Strong fit for gated content operations        |
| Bulk registration triage | Speeds up request approval/rejection and queue cleanup                                  | Medium              | Strong fit for existing registration actions   |
| Public preview command   | Lets teams verify request/gated states without a live protected page                    | Low                 | Fits existing request views and doctor command |

Risks and follow-ups:

- Screenshot data needs seeded areas, registrations, grants, tokens, and events.
- Confirm package install publishes/runs migrations before final screenshots.

## Address

Composer name: `capell-app/address`
Hard dependencies: `capell-app/admin`
Optional dependencies: none
Surfaces: admin, console, database, shared form components

Observed surfaces:

- Admin resources: `AddressResource`, `CountryResource`.
- Admin pages: `ManageAddresses`, `ManageCountries`.
- Form components: `AddressSelect`, `CountrySelect`, `FlagSelect`.
- Site form schema extender for address fields.
- Blade component: `components/flag-icon`.

Install verification:

- Composer require succeeded and package discovery listed `capell-app/address`.
- Address commands were visible: `capell:address-demo`, `capell:address-faker`, `capell:address-install`.
- `php artisan migrate --graceful` reported `Nothing to migrate` before extension install, despite schema-owning migrations.

Docs updated:

- Corrected `packages/address/docs/screenshots.json` targets for countries and site schema extender entries.

Feature suggestions:

| Suggestion                       | User value                                                               | Implementation risk | Package fit                               |
| -------------------------------- | ------------------------------------------------------------------------ | ------------------- | ----------------------------------------- |
| Country import/update command    | Keeps country data complete and repeatable across installs               | Low                 | Fits existing install/demo/faker commands |
| Address usage panel              | Shows which sites and packages reference an address before edits/deletes | Medium              | Fits current site relationship count      |
| Site address completeness widget | Helps admins spot missing address metadata for public sites              | Low                 | Fits site schema extender                 |

Risks and follow-ups:

- Verify install command/migration sequence in a fresh app before screenshots.
- Final screenshots need seeded countries, addresses, and a site using address fields.

## Agent Bridge

Composer name: `capell-app/agent-bridge`
Hard dependencies: `capell-app/admin`, `capell-app/core`
Optional dependencies: none
Surfaces: admin, Agent Bridge/MCP routes, database

Observed surfaces:

- Filament page: `CapellAgentBridgePromptBuilderPage` at `admin/capell-agent-bridge/prompt-builder`.
- User relation managers: tokens, confirmations, audit entries.
- Settings schema: `AgentBridgeSettingsSchema`.
- Routes: optional home route plus MCP knowledge/site endpoints configured in `routes/agent-bridge.php`.

Install verification:

- Composer require succeeded and package discovery listed `capell-app/agent-bridge`.
- Extension install command ran.
- Route list showed `GET|HEAD admin/capell-agent-bridge/prompt-builder`.
- `php artisan migrate --graceful` reported `Nothing to migrate` before extension install, despite schema-owning migrations.

Docs updated:

- Fixed `packages/agent-bridge/docs/overview.md` package manifest metadata.
- Added relation managers and settings schema to the overview admin surface list.

Feature suggestions:

| Suggestion                 | User value                                                                  | Implementation risk | Package fit                          |
| -------------------------- | --------------------------------------------------------------------------- | ------------------- | ------------------------------------ |
| Token setup wizard         | Makes secure Agent Bridge setup less error-prone                            | Medium              | Fits token model and settings schema |
| Capability dry-run history | Lets admins review what agents proposed before confirmation                 | Medium              | Fits confirmation and audit models   |
| MCP endpoint health panel  | Shows enabled routes, auth guard, and last successful authenticated request | Medium              | Fits route and audit surfaces        |

Risks and follow-ups:

- Final screenshots need seeded tokens, confirmations, and audit records.
- Confirm migrations are published/running in the extension install flow.

## AI Orchestrator

Composer name: `capell-app/ai-orchestrator`
Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/layout-builder`
Optional dependencies: none
Surfaces: shared/admin services, no standalone UI

Observed surfaces:

- No Filament resource/page/widget or public route in this package by itself.
- Registers AI module registry services and a Layout Builder integration module.
- Actions: list, register, and run capabilities.

Install verification:

- Composer install was attempted in the batch sequence with hard dependencies.
- No standalone route or visible admin surface is expected from this package alone.

Docs updated:

- Corrected `packages/ai-orchestrator/docs/overview.md` hard dependency list.
- Changed `packages/ai-orchestrator/docs/screenshots.json` entries to non-required because this package has no standalone visible UI.

Feature suggestions:

| Suggestion                              | User value                                                               | Implementation risk | Package fit                               |
| --------------------------------------- | ------------------------------------------------------------------------ | ------------------- | ----------------------------------------- |
| Capability registry diagnostics command | Helps developers confirm modules and capabilities are registered         | Low                 | Fits service-only package                 |
| Approval policy manifest                | Documents which capabilities require review before execution             | Medium              | Fits existing approval enum/data boundary |
| Sample consuming admin page fixture     | Gives screenshot runner a stable surface without inventing production UI | Medium              | Useful for package docs and demos         |

Risks and follow-ups:

- Do not require screenshots for this package alone; capture UI through consuming packages.

## API

Composer name: `capell-app/api`
Hard dependencies: `capell-app/core`, `capell-app/layout-builder`
Optional dependencies: none
Surfaces: frontend JSON routes

Observed surfaces:

- Routes: `GET /api/capell/pages/resolve`, `GET /api/capell/v1/pages/resolve`.
- Controller returns public JSON with API version and cache-tag headers.
- Explicit site/language context requires a valid signature.
- Layout output depends on Layout Builder and bounded container requests.

Install verification:

- Composer install was attempted in the batch sequence with Layout Builder.
- Full endpoint screenshot verification still needs a seeded published page and installed-package marker.

Docs updated:

- Added `packages/api/docs/overview.md`.
- Added `packages/api/docs/screenshots.json`.
- Linked overview and screenshot contract from `packages/api/README.md`.

Feature suggestions:

| Suggestion                 | User value                                                           | Implementation risk | Package fit                       |
| -------------------------- | -------------------------------------------------------------------- | ------------------- | --------------------------------- |
| API diagnostics admin page | Shows endpoint health, installed flag, middleware, and cache headers | Medium              | Strong fit for route-only package |
| Typed response examples    | Helps consumers build integrations without reading controller code   | Low                 | Documentation-focused improvement |
| Endpoint explorer command  | Generates redacted sample JSON from a selected page                  | Low                 | Fits public JSON delivery         |

Risks and follow-ups:

- Capture success, not-found, forbidden, and layout responses with seeded pages.
- Verify anonymous JSON never exposes authoring metadata or internal editor fields.

## Content Blocks

Composer name: `capell-app/content-blocks`
Repository path: `packages/block-library`
Hard dependencies: `capell-app/core`
Optional dependencies: `capell-app/content-sections`, `capell-app/foundation-theme`
Surfaces: shared foundation, no standalone UI

Observed surfaces:

- Registry and actions for block definitions.
- Contracts for definition providers, renderers, fixture providers, demo content, and Filament builder blocks.
- Fallback block Blade view.
- No migrations, routes, admin navigation, or standalone public output.

Install verification:

- Composer require succeeded and package discovery listed `capell-app/content-blocks`.
- Extension install command ran.
- No routes or migrations are expected.

Docs updated:

- Added `packages/block-library/docs/overview.md`.
- Added `packages/block-library/docs/screenshots.json`.
- Linked overview and screenshot contract from `packages/block-library/README.md`.

Feature suggestions:

| Suggestion                          | User value                                                                  | Implementation risk | Package fit                    |
| ----------------------------------- | --------------------------------------------------------------------------- | ------------------- | ------------------------------ |
| Block registry diagnostics command  | Makes registered providers/renderers visible to developers                  | Low                 | Strong fit for shared registry |
| Admin-only fixture preview page     | Gives package docs and QA a concrete render target                          | Medium              | Fits block fixture contracts   |
| Definition schema validation report | Catches missing labels, views, categories, and accessibility metadata early | Medium              | Fits typed definition data     |

Risks and follow-ups:

- Standalone screenshots should stay optional; meaningful captures require a consuming package.

## Blog

Composer name: `capell-app/blog`
Hard dependencies: `capell-app/admin`, `capell-app/content-sections`, `capell-app/core`, `capell-app/demo-kit`, `capell-app/frontend`, `capell-app/html-cache`, `capell-app/insights`, `capell-app/layout-builder`, `capell-app/navigation`, `capell-app/publishing-studio`, `capell-app/site-discovery`, `capell-app/tags`
Optional dependencies: none
Surfaces: admin, frontend components, console, database

Observed surfaces:

- Admin resource: `ArticleResource` with index/create/edit pages.
- Dashboard widgets: article health, list articles, top pages, traffic chart abstractions.
- Frontend Livewire pages and views for blog, archive, tag, article metadata, footers, and blocks.
- Commands: install, setup, demo, faker, create pages.

Install verification:

- Composer require succeeded and installed hard dependency packages.
- `capell:extension-install capell-app/blog` was blocked because required dependency packages were Composer-installed but not marked installed as Capell extensions.
- Error listed missing required plugins: content sections, demo kit, html cache, insights, layout builder, navigation, publishing studio, site discovery, tags.

Docs updated:

- Added dashboard widget and block capture entries to `packages/blog/docs/screenshots.json`.
- Updated `packages/blog/docs/overview.md` screenshot plan.

Feature suggestions:

| Suggestion                     | User value                                                            | Implementation risk | Package fit                         |
| ------------------------------ | --------------------------------------------------------------------- | ------------------- | ----------------------------------- |
| Blog setup checklist page      | Shows missing layouts, pages, tags, navigation, and sitemap readiness | Medium              | Fits broad install dependencies     |
| Editorial article health score | Helps editors improve metadata, media, tags, and publish windows      | Medium              | Fits existing health widget concept |
| Archive/tag preview matrix     | Lets teams verify archive and tag pages across sites/languages        | Medium              | Fits frontend Livewire surfaces     |

Risks and follow-ups:

- The installer should either install hard dependency extensions first or document the required order clearly.
- Final screenshots require seeded articles, tags, pages, layouts, and navigation.

## Campaign Studio

Composer name: `capell-app/campaign-studio`
Hard dependencies: `capell-app/admin`, `capell-app/core`, `capell-app/form-builder`, `capell-app/frontend`, `capell-app/insights`, `capell-app/layout-builder`
Optional dependencies: `capell-app/seo-suite`
Surfaces: admin, frontend blocks, dashboard widgets, database

Observed surfaces:

- Admin resources: campaign groups, landing pages, conversion goals, CTA blocks.
- Admin pages: index/create/edit for each resource.
- Dashboard widgets: overview stats, top campaign studio, top landing pages.
- Layout Builder configurators for campaign hero, CTA, and lead-form blocks.
- Page schema extender for campaign fields.
- Frontend block views and tracking attributes component.

Install verification:

- Composer require succeeded and installed hard dependency packages.
- Initial `capell:extension-install capell-app/campaign-studio` was blocked because dependency packages were not marked installed.
- Follow-up attempted `capell:extension-install` for Layout Builder, Insights, and Form Builder; Layout Builder published and ran migrations, but Campaign Studio still reported Layout Builder, Insights, and Form Builder as missing.

Docs updated:

- Corrected hard dependency docs in `packages/campaign-studio/docs/overview.md`.
- Corrected screenshot targets in `packages/campaign-studio/docs/screenshots.json`.

Feature suggestions:

| Suggestion                       | User value                                                                             | Implementation risk | Package fit                         |
| -------------------------------- | -------------------------------------------------------------------------------------- | ------------------- | ----------------------------------- |
| Campaign launch checklist        | Shows missing goals, landing page links, forms, UTM settings, and tracking readiness   | Medium              | Strong fit for growth workflow      |
| Conversion attribution inspector | Helps marketers debug which goal, landing page, visit, and event produced a conversion | Medium              | Fits conversion model relationships |
| Campaign block preview library   | Lets editors preview hero, CTA, and lead-form blocks before publishing                 | Medium              | Fits existing block configurators   |

Risks and follow-ups:

- Extension install dependency state needs investigation before final screenshots.
- Final screenshots require seeded groups, landing pages, conversion goals, CTA blocks, forms, visits/events, and frontend pages.
