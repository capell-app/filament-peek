# Optional Package Boundaries

Composer availability is not Capell extension availability. A package class can autoload while the extension is not installed, disabled, or missing its tables.

Do not use `class_exists()` as the only guard before calling optional Capell package runtime code:

```php
if (class_exists(Navigation::class)) {
    Navigation::query()->first();
}
```

Use Capell's installed-state check first:

```php
if (CapellCore::isPackageInstalled('capell-app/navigation') && class_exists(Navigation::class)) {
    Navigation::query()->first();
}
```

If the code can run during install, upgrade, diagnostics, or another partial database state, also guard the table before querying:

```php
if (
    CapellCore::isPackageInstalled('capell-app/navigation')
    && Schema::hasTable('navigations')
    && class_exists(Navigation::class)
) {
    Navigation::query()->first();
}
```

This applies to models, Actions, Blade components, Filament fields, listeners, render hooks, and service-provider registrations that touch package runtime behavior.

`class_exists()` is still appropriate for non-Capell PHP/library capabilities, dynamic configured classes, autoload priming before cache deserialization, and defensive validation after the Capell package has already been proven installed.

## Frontend runtime contributors

Optional packages must not make `capell/frontend` import their models or Actions to decide public runtime requirements. Use `Capell\Frontend\Contracts\FrontendRuntimeManifestContributor` instead.

Example: layout-builder registers `LayoutBuilderRuntimeManifestContributor` during package registration. The contributor inspects layout-builder element data and adds the `layout-builder`, Alpine, Livewire, and island flags to `FrontendRuntimeManifestData`. Frontend only loops over tagged contributors and remains installable without layout-builder.

## Layout Builder Areas

Theme or frontend packages that need editor-managed content in page chrome should use Layout Builder areas instead of hidden containers, render-hook side effects, or package-owned placement tables.

Register named areas through `Capell\LayoutBuilder\Support\LayoutAreas\LayoutAreaRegistry` only when `capell-app/layout-builder` is an installed dependency for that package path. The built-in `main` area is always available. Missing container `meta.area` values are treated as `main`, so older layouts continue to render in the normal content loop.

```php
use Capell\LayoutBuilder\Support\LayoutAreas\LayoutAreaRegistry;

$this->app->afterResolving(
    LayoutAreaRegistry::class,
    function (LayoutAreaRegistry $registry): void {
        $registry->register(
            key: 'header',
            label: __('capell-layout-builder::generic.header_area'),
        );
    },
);
```

If the area belongs to one active theme, pass the theme key so the editor select only shows the area for that theme:

```php
$registry->register(
    key: 'product-nav',
    label: __('capell-product::layout_areas.product_nav'),
    themeKey: 'product',
);
```

Public rendering should use the theme area component:

```blade
<x-capell::layout.area area="header" />
```

Do not query layout containers, elements, pages, media, or package models from public Blade to render an area. Build or preload the public render data before the view layer, and keep editor-only metadata out of anonymous HTML and static caches.
