# Hero Overview

Hero provides the shared default home-page hero block used by Capell frontend themes. It registers Blade components and a setup command that can seed Hero-managed home layout defaults.

## What It Adds

- `capell::block.hero` Blade component.
- Anonymous `capell-hero::...` component namespace for hero partials.
- Tailwind import and view-source registration for hero assets.
- `capell:hero-setup` setup command.
- No package-owned tables, routes, settings, or Filament resources.

## Install Impact

- Requires `capell-app/core`, `capell-app/frontend`, and `capell-app/layout-builder`.
- Adds no migrations.
- Adds frontend rendering only when a layout/theme uses the Hero block component or after the setup command creates default layout content.

## Admin Surfaces

None directly. Editors interact with Hero through Layout Builder content after the setup command or host demo data creates the block. Hero itself registers no `src/Filament` classes.

## Frontend Surfaces

| Surface                     | Use case                                                                  | Screenshot           |
| --------------------------- | ------------------------------------------------------------------------- | -------------------- |
| Home hero block             | Show the default hero block rendered in a public theme/page.              | `hero-home-block`    |
| Hero slide/related partials | Show multi-item hero content if seeded by the setup flow or demo fixture. | `hero-slide-variant` |

## Demo Setup

Install core baseline packages, hard dependencies, and `capell-app/hero`. Run `capell:hero-setup` in the host app if the demo needs seeded layout defaults. Capture a public page using a theme that renders the hero component.

## Screenshot Coverage

The screenshot contract should prove the public block output and, when seeded, any slide/related content states. There is no standalone admin screen to capture.

## Known Risks

- A package install alone may not create visible output; the setup command or demo content is required.
- The block relies on a theme/layout to call the component, so captures should name the theme fixture used.
- Hero asset registration should be checked when frontend Tailwind assets are regenerated.

## Verification

Run package tests from the repository root:

```bash
vendor/bin/pest packages/hero/tests --configuration=phpunit.xml
```
