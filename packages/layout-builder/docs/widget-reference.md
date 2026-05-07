# LayoutBuilder Widget Reference

This focused guide extends [Overview](overview.md) for the LayoutBuilder package.

## Purpose

LayoutBuilder owns reusable widgets, sections, widget assets, and layout container placement.

Use this guide for widget families and editor-facing behaviour that would crowd the package README.

## Widget Families

- Hero and hero banner widgets.
- Card grid, feature list, FAQ, image gallery, pricing table, process steps, stats, team members, and testimonial widgets.
- Page content, navigation, result, system, and asset-backed widgets.
- Campaign and Blog packages can register LayoutBuilder-aware widget configurators.

## Admin Workflow

1. Open the Widgets resource.
2. Create or edit a widget with a registered widget type.
3. Attach assets where the configurator supports them.
4. Place the widget in a layout container or section.
5. Verify frontend rendering and layout cache behaviour.

## Extension Points

- Register widget types in LayoutBuilder service provider registration.
- Add widget configurators for package-owned widget data.
- Register asset-backed frontend renderers through `FrontendComponentRegistryInterface` using stable keys such as `section.block` and `section.team-member`.
- Keep domain work in actions such as `MakeWidgetAction`, `ApplyLayoutPlanAction`, and `AddWidgetToLayoutContainerAction`.
- Keep rendering in Blade or Livewire components rather than Filament resources.

## Frontend Component Overrides

LayoutBuilder renders asset-backed section items through the frontend component registry. Stored data should use stable component keys, not package Blade namespaces:

```php
'component_item' => 'section.block',
```

At boot, packages register the Blade implementation for those keys. Content Sections provides neutral defaults, LayoutBuilder registers its own templates, and theme packages may register a later override:

```php
use Capell\Frontend\Contracts\FrontendComponentRegistryInterface;

$this->callAfterResolving(
    FrontendComponentRegistryInterface::class,
    fn (FrontendComponentRegistryInterface $registry): FrontendComponentRegistryInterface => $registry
        ->register(
            key: 'section.team-member',
            component: 'capell-example-theme::section.team-member',
            aliases: [
                'capell-content-sections::section.team-member',
                'capell-layout-builder::section.team-member',
            ],
            props: [
                'asset',
                'class',
                'color',
                'icon',
                'image',
                'linkText',
                'loop',
                'meta',
                'size',
                'summary',
                'title',
                'url',
            ],
        ),
);
```

Always include legacy Blade component names as aliases when replacing an existing renderer. That allows older saved content to resolve through the new implementation while new content stores only the stable key.

## Screenshot Requirements

- Widget index.
- Widget create/edit form.
- Asset relation manager.
- Layout builder placement.
- Frontend output for each modern widget family.
