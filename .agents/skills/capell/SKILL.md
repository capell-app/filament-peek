---
name: capell
description: Capell Packages coding standards, architecture rules, and package conventions. Use when writing or reviewing any code in capell-packages-4.
---

# Capell Packages — Coding Skill

## Non-negotiables

- `declare(strict_types=1);` in every PHP file.
- PHP 8.2 only — no typed class constants, no readonly classes, no DNF types.
- No `php artisan` — use `vendor/bin/pest` directly; `orchestral/testbench` provides the Laravel context.
- No single-letter or cryptic variable names — ever, including closures and migrations.
- All closures must declare parameter types and return types explicitly (`: void` when mutating a passed-in object).

## Architecture: Actions + Data (reach for these first)

**All domain logic lives in Actions** (`packages/{pkg}/src/Actions/`):

- Suffix: `VerbNounAction` (`CreateBlogPostAction`, `PublishContentWidgetAction`).
- Single `handle()` method. Split by verb, never god-actions.
- Extend `Lorisleiva\Actions\Action` or use `AsObject` trait.
- Components, resources, commands call `::run()` — no domain logic inside them.

**Pass structured data across layer boundaries** (`packages/{pkg}/src/Data/`, suffix `Data`):

- Inbound: `Data::from($request)` — no `$request->input()` in actions.
- Outbound: Filament form state, Livewire wire-props, Blade view models.
- Model JSON/struct columns cast via `AsData` / `AsDataCollection`.
- No DTOs wrapping a single scalar.

**Enums** (`packages/{pkg}/src/Enums/`):

- Backed enums for persisted values (prefer string-backed).
- PascalCase multi-word cases; UPPER_SNAKE_CASE for status/state flags only.
- Implement `HasLabel` for Filament Select/Radio options — never inline option arrays.
- Type-hint enums in all signatures; never pass raw scalars where an enum exists.

## Frontend authoring safety

- Non-admin frontend users must never receive editor HTML, JavaScript, metadata, selectors, model IDs, field paths, package hints, or signed editor URLs.
- In-page authoring is added only after page load, from an authenticated admin beacon response.
- Do not render authoring markers into public Blade, theme output, cached HTML, or package assets.
- When a package touches frontend output, beacon behaviour, page cache, or theme code, keep tests that prove anonymous and non-admin users see no authoring surface.

## Packages in this repo

This repo contains many Capell add-on packages. Treat `composer.json` and `composer.local.json` PSR-4 autoload entries as the current source of truth for package namespaces and test namespaces.

Common active packages include `layout-builder`, `blog`, `address`, `ai-orchestrator`, `block-library`, `campaign-studio`, `content-sections`, `frontend-authoring`, `login-audit`, `media-ai`, `publishing-studio`, `seo-suite`, `theme-studio-*`, `toolbar`, and others under `packages/`.

**Blog requires LayoutBuilder — install LayoutBuilder first.**

## Package boundaries (strict)

- **Core must never import plugin classes** — no `use Capell\Blog\...`, `use Capell\LayoutBuilder\...` from Core.
- Cross-plugin coordination uses events, Artisan command name strings, or shared filesystem paths.
- Packages should not reach into each other's internals (Arch tests enforce this).

## Extension points — use these, don't bypass them

| Need                                   | Use                                                                                               |
| -------------------------------------- | ------------------------------------------------------------------------------------------------- |
| Register page type / schema / widget   | `CapellCore::registerPageType\|registerSchema\|registerWidget()` in `ServiceProvider::register()` |
| Inject Filament form fields            | Implement `PageSchemaExtender`, tag with `PageSchemaExtender::TAG`                                |
| Lifecycle callbacks / validation gates | `CapellAdmin::register()` / `subscribe()` / `ValidationSubscriber`                                |
| Inject HTML into Blade                 | `RenderHookRegistry::register(RenderHookLocation::X, ...)`                                        |
| Expose package settings                | `SettingsSchemaRegistry::register()` + `registerSettingsClass()`                                  |

Auto-discovered: types in `src/Types/`, schemas in `src/Schemas/`, widgets in `src/Widgets/`.

## PublishingStudio / Draftable

Any package model in draft/publish must implement `Capell\Core\Contracts\Draftable` and be registered in the morph map in the package's service provider. Reuse `ReplicateModelAction`, `ReplicatePageAction` — don't reinvent replication.

## Testing

- Test actions directly: `MyAction::run($input)` — not through HTTP.
- Start with the narrowest useful Pest command, usually one test file or one package: `vendor/bin/pest packages/{package}/tests --configuration=phpunit.xml`.
- Run a single package: `vendor/bin/pest packages/layout-builder/tests --configuration=phpunit.xml`
- Minimum 80% coverage. Full suite: `composer test`.
- Arch tests enforce package boundaries — don't suppress them.

## Commit checklist

1. `composer test` — 100% pass.
2. `composer preflight:all` — Rector + Pint + PHPStan + tests clean.
3. Verify in demo workbench (`composer serve`) before committing.
4. No short variable names in the diff.
5. Commit immediately after task completion.

## Key commands

| Command                                    | Purpose                                   |
| ------------------------------------------ | ----------------------------------------- |
| `composer test`                            | Pest tests (parallel)                     |
| `composer preflight`                       | Changed-file formatting plus full PHPStan |
| `composer preflight:all`                   | Rector + full Pint + PHPStan + tests      |
| `composer lint`                            | Pint only                                 |
| `composer analyze`                         | PHPStan only                              |
| `composer prepare`                         | Seed demo workbench                       |
| `composer serve`                           | Build + serve at localhost:8000           |
| `vendor/bin/pest packages/{package}/tests` | Run single package tests                  |

## Agent speed notes

- Keep task branches focused where possible. Large dirty trees across many packages slow agents down because unrelated user work must be preserved.
- Use package-level or file-level Pest runs during implementation; save `composer test`, `composer analyze`, and `composer preflight:all` for final verification.
- The local Composer overlay can be a faster daily-driver setup because it path-links sibling Capell packages and may use fail-fast test settings.
