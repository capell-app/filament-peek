# Filament Peek reference

<!-- prettier-ignore-start -->

Developer reference for `capell-app/filament-peek`. Start with the [README](../README.md) for what the extension does and how to install it.

## Package Details

Filament Peek is an **Available**, **No schema impact** Capell package in the **Capell Essentials** product group. It ships as `capell-app/filament-peek` and extends these surfaces: admin, frontend.

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/filament-peek`
- Namespace: `Capell\FilamentPeek`
- Theme key: not applicable

## Technical Shape

### Service providers

- `Capell\FilamentPeek\Providers\FilamentPeekServiceProvider`

### Config files

- `packages/filament-peek/config/capell-filament-peek.php`

### Filament classes

- `PeekPagePreviewAction`
- `FilamentPeekPanelExtender`
- `PagePeekPreviewActionExtender`

### Route files

- `packages/filament-peek/routes/web.php`

### Extension contracts

- `StoresLayoutBuilderPreviewState`

### Actions

- `CreatePagePreviewSnapshotAction`
- `FindPagePreviewSnapshotAction`
- `RegisterLayoutBuilderPreviewWidgetsAction`
- `RenderPagePreviewSnapshotAction`
- `StoreLayoutBuilderPreviewStateAction`

### Data objects

- `LayoutBuilderPreviewStateData`
- `PagePreviewSnapshotData`

### Manifest contributions

- `route: Capell\FilamentPeek\Manifest\FilamentPeekRoutesContribution`

### Health checks

- `Capell\FilamentPeek\Health\FilamentPeekHealthCheck`

### Blade views

- `packages/filament-peek/resources/views/page-preview-modal.blade.php`
- `packages/filament-peek/resources/views/preview-error.blade.php`
- `packages/filament-peek/resources/views/preview-ribbon.blade.php`

### Cache tags

- `filament-peek-preview`


## Data Model

This package has no schema impact. It extends Capell through `route` contributions instead of declaring package-owned tables.

<!-- prettier-ignore-end -->
