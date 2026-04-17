# Capell Hero

A page hero section for Capell — a full-width header area with a title, subtitle, background image, and optional call-to-action. Ships as both a **widget** (for the layout builder) and a **page schema extender** (so any page type can opt into an inline hero field).

## What this package adds

- **Hero widget** renderable in the layout builder via `capell-hero::components.widget.hero`.
- **Hero content schema** and **Hero widget schema** registered with the Capell schema registry.
- **`HeroEditor` form component** that adds hero fields inside Filament page schemas.
- **`HeroPageSchemaExtender`** that injects the hero editor at the `AfterTitle` position of page translation forms.

No new database tables — hero data is stored in the Layout package's `contents` / `widgets` / `widget_assets` tables or inside the page translation `meta` JSON.

## Prerequisites

- `capell-app/admin`
- `capell-app/layout`

(Frontend package is pulled in transitively via Admin.)

## Installation

```sh
php artisan capell:hero-setup
```

The setup command registers the hero widget on the default layout and publishes translations and vendor assets.

Seed demo hero content for one or more sites:

```sh
php artisan capell:hero-demo --sites=1
```

> **Note on command naming.** This package uses `capell:hero-setup`, not `capell:hero-install`. There is no separate install command — the service provider handles registration on boot, and `setup` wires the widget into an existing layout.

## How it's used

### As a layout-builder widget

After setup, editors will find "Hero" in the widget picker inside the layout builder. It ships with a standard schema for title, subtitle, background media, and CTAs. Rendered via:

```blade
<x-capell-hero::components.widget.hero :widget="$widget" />
```

### As a page schema field

`HeroPageSchemaExtender` is registered through the Capell hook system. When a compatible page schema fires the `PageTranslationSchemaHookEnum::AfterTitle` hook, the extender injects the `HeroEditor` form component — so the hero fields show up directly on the page's translation tab.

You don't need to call it manually; importing the class is enough to activate it (it self-registers in the provider):

```php
use Capell\Hero\Filament\Extenders\Page\HeroPageSchemaExtender;
```

## Database

None. See [docs/Database.md](docs/Database.md).

## Artisan commands

| Command | Purpose |
| --- | --- |
| `capell:hero-setup` | Register the hero widget on the default layout, publish assets |
| `capell:hero-demo` | Seed demo hero content (`--sites=`) |

## Further reading

- [Database reference](docs/Database.md) — (no tables; points at Layout)
- [API reference](docs/API.md) — service provider, editor, enums, commands
- Capell core docs: [Packages overview](../../../capell-4/docs/packages.md)
