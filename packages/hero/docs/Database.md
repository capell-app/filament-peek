# Database Reference — Capell Hero

**The Hero package ships no migrations.** It stores data inside the Layout package's existing tables:

- `contents` — when the hero is used as reusable content
- `widgets` — when the hero is placed as a widget in a layout
- `widget_assets` — for the hero's background media

And inside the core page translation `meta` JSON column (`page->translation->meta['hero']`) when the hero is attached to a page via `HeroPageSchemaExtender` rather than placed as a widget.

## Prerequisite

Install Layout first, so the storage tables exist:

```sh
php artisan capell:layout-install
```

Then set up Hero:

```sh
php artisan capell:hero-setup
```

## See also

- Layout database reference: [`../../layout/docs/Database.md`](../../layout/docs/Database.md)
