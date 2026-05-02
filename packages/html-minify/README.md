# HTML Minify

Status: **Available, no schema impact** · Kind: **package** · Tier: **free** · Bundle: **foundation** · Contexts: **frontend** · Product group: **Capell Foundation**

## What This Plugin Adds

HTML Minify adds middleware and support code for reducing frontend HTML output and page-cache writes.

- HtmlMinifyMiddleware.
- HtmlMinifier support class.
- Service provider for registration.

## Why It Matters

**For developers:** Provides a small rendering concern that can be attached to frontend responses without changing page or layout models.

**For teams:** Reduces HTML payload size where the site wants smaller cached responses and cleaner output.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Frontend page before/after HTML output inspection.
- Middleware configuration or service provider registration proof.

## Technical Shape

- HtmlMinifyServiceProvider registers the package.
- Http middleware: HtmlMinifyMiddleware.
- Support class: HtmlMinifier.
- No migrations, config file, routes, resources, or models are present.

## Data Model

- This package does not own data.
- It transforms response content at render time.

## Install Impact

- Adds middleware capability.
- No database changes.
- No admin navigation.
- No public routes.

## Commands

- None proven in this package directory.

## Admin And Access

- None proven in this package directory.

- None proven in this package directory.

## Common Pitfalls

- Do not minify responses that contain whitespace-sensitive content without testing.
- Confirm middleware order with page cache middleware.
- Inspect HTML comments or inline scripts if output changes unexpectedly.

## Quick Start

1. Install the package with `composer require capell-app/html-minify`.
2. Register the package provider through Composer discovery and clear cached config if the host app uses config caching.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../default-theme/README.md](../default-theme/README.md)
- [../toolbar/README.md](../toolbar/README.md)
