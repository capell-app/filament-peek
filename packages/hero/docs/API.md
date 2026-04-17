# API Reference — Capell Hero

Browse `src/` for full source. This page is a map of the key entry points.

## Service provider

- `src/Providers/HeroServiceProvider.php` — extends `AbstractPackageServiceProvider`. Registers schemas, Blade components, schema extenders, and commands.

## Schemas

Registered in `HeroServiceProvider::registerSchemas()`:

- `ContentSchemaEnum::Hero` → `src/Filament/Resources/Contents/Schemas/Types/HeroContentSchema.php`
- `WidgetSchemaEnum::Hero` → `src/Filament/Resources/Widgets/Schemas/Types/HeroWidgetSchema.php`

## Form component

- `src/Filament/Components/Forms/Page/HeroEditor.php` — Filament form component with the hero fields (title, subtitle, media, CTAs). Used inside page translation forms.

## Schema extender

- `src/Filament/Extenders/Page/HeroPageSchemaExtender.php` — injects `HeroEditor` at `PageTranslationSchemaHookEnum::AfterTitle` on pages that support it. Registered in `HeroServiceProvider::bootInstalledPackage()`.

## Blade view component

- `src/View/Components/Widget/Hero.php` — extends `AbstractWidget`.
- Default view: `capell-hero::components.widget.hero`.

View namespace registered via `Blade::componentNamespace('Capell\Hero\View\Components', 'capell-hero')`.

Views under `resources/views/`:

- `components/widget/hero.blade.php` — widget template
- `components/hero/wrapper.blade.php`
- `components/hero/content.blade.php`
- `components/hero/slide.blade.php`
- `components/hero/related.blade.php`
- `components/pagination/summary.blade.php`

## Enums

- `src/Enums/ContentSchemaEnum.php` — adds `Hero` case
- `src/Enums/WidgetSchemaEnum.php` — adds `Hero` case
- `src/Enums/WidgetComponentEnum.php`
- `src/Enums/WidgetTypeEnum.php`

## Actions

Under `src/Actions/`:

- `AddHeroToLayoutAction` — used by `capell:hero-setup` to place the hero widget on the default layout
- `CreateHeroWidgetAction` — create a new hero widget instance
- `CreateHeroContentTypeAction` — create the Hero content type row
- `HeroWidgetHasPrimaryHeadingAction` — detects whether a placed hero already carries the page's H1

## Commands

Under `src/Console/Commands/`:

- `SetupCommand` — `capell:hero-setup`
- `DemoCommand` — `capell:hero-demo`

There is **no** `capell:hero-install` command.

## Composer dependencies

- `capell-app/admin`
- `capell-app/layout`

## Quick links

- Source directory: [`./src`](../src)
- Database reference: [Database.md](Database.md) (no tables)
- Package README: [../README.md](../README.md)
