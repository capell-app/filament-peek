# SEO Tools

Status: **Available, schema-owning** · Kind: **package** · Tier: **premium** · Bundle: **search-seo** · Contexts: **admin, frontend, console** · Product group: **Capell Search & SEO**

## What This Plugin Adds

SEO Tools adds metadata panels, sitemap generation, structured data, broken link tracking, Search Console insights, AI-assisted content briefs, and publish checks.

- Page and site SEO schema extenders.
- SEO audit, broken links, not-found URLs, sitemap, and translation coverage pages.
- Sitemap Livewire page and tool component.
- AI creator actions for briefs, images, layouts, metadata suggestions, and draft application.
- Search Console sync and reports.

## Why It Matters

**For developers:** Exposes SEO work as actions, contracts, data objects, settings schemas, and extenders that connect to core pages, sites, translations, routes, and optional AI providers.

**For teams:** Gives editors and site operators practical checks before publishing and operational reports after launch.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- Page SEO panel.
- SEO audit page.
- Broken links page.
- Sitemap page.
- Translation coverage page.
- AI creator action modal.
- Search Console insights panel.

## Technical Shape

- SeoToolsServiceProvider registers settings, pages, extenders, commands, routes, and views.
- Config files: capell-seo-tools.php and exchanger.php.
- Migrations create broken links, page SEO snapshots, Search Console metrics, AI creator contexts, AI histories, and AI sessions.
- Commands cover install, setup, sitemap, AI cache, AI usage, and OpenAI connection testing.
- Controller: LlmsTxtController.

## Data Model

- broken_links stores page, target URL, HTTP status, and last check time.
- page_seo_snapshots store page SEO report state.
- search_console_url_metrics store imported Search Console values.
- ai_creator_contexts, ai_generation_histories, and ai_creator_sessions store AI workflow state.
- SEO data connects to sites, pages, languages, users, and workspaces.

## Install Impact

- Adds SEO and AI-related tables/settings.
- Extends page and site admin forms.
- Adds SEO admin pages and widgets.
- Adds sitemap and llms.txt frontend output.
- Adds config for AI provider/model, image model, Search Console, publish gates, and prompts.

## Commands

- `capell:admin-clear-ai-cache` (packages/seo-tools/src/Console/Commands/ClearAiCacheCommand.php)
- `capell:seo-tools-install` (packages/seo-tools/src/Console/Commands/InstallCommand.php)
- `capell:admin-monitor-ai-usage` (packages/seo-tools/src/Console/Commands/MonitorAiUsageCommand.php)
- `capell:seo-tools-setup` (packages/seo-tools/src/Console/Commands/SetupCommand.php)
- `capell:admin-test-openai` (packages/seo-tools/src/Console/Commands/TestOpenAiConnectionCommand.php)
- `capell:xml-sitemap {--site= : Only regenerate sitemaps for this site ID} {--incremental : Skip domains whose pages have not changed since the last run}` (packages/seo-tools/src/Console/Commands/XmlSitemapCommand.php)

## Admin And Access

- BrokenLinksPage (packages/seo-tools/src/Filament/Pages/BrokenLinksPage.php, slug `broken-links`)
- NotFoundUrlsPage (packages/seo-tools/src/Filament/Pages/NotFoundUrlsPage.php, slug `missing-pages`)
- SEOAuditPage (packages/seo-tools/src/Filament/Pages/SEOAuditPage.php, slug `seo-audit`)
- SitemapPage (packages/seo-tools/src/Filament/Pages/SitemapPage.php, slug `sitemap`)
- TranslationCoveragePage (packages/seo-tools/src/Filament/Pages/TranslationCoveragePage.php, slug `translation-coverage`)

- Policy: AiCreatorPolicy (packages/seo-tools/src/Policies/AiCreatorPolicy.php)
- Gate: AiMetricsWidgetAbstract: `developer`, `admin`, `super_admin`
- Gate: BrokenLinksPage: Filament Shield page permissions
- Gate: NotFoundUrlsPage: Filament Shield page permissions
- Gate: SEOAuditPage: Filament Shield page permissions
- Gate: SitemapPage: Filament Shield page permissions
- Gate: TranslationCoveragePage: Filament Shield page permissions

## Common Pitfalls

- Do not enable AI creator without checking provider credentials and review workflow.
- Search Console requires credentials and property URL.
- Publish gates can block publishing when required metadata is missing.
- Regenerate sitemap output after route or content changes.

## Quick Start

1. Install the package with `composer require capell-app/seo-tools`.
2. Run the package migrations or the Capell package installer required by the host app.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../redirects/README.md](../redirects/README.md)
- [../blog/README.md](../blog/README.md)
- [../workspaces/README.md](../workspaces/README.md)
