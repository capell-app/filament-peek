# Capell Packages — cross-cutting docs

This directory holds **cross-cutting** documentation for the add-on packages. Per-package docs (API, Database, README) live alongside each package in `packages/<name>/`.

## Per-package references

For the commercial/free grouping, see [Package product groups](product-groups.md).
For package-level upstream credits, services, and acknowledgements, see [Credits and acknowledgements](credits-and-acknowledgements.md).
For theme package authoring, see [Creating a Capell theme](creating-a-theme.md).
For optional package integration rules, see [Optional Package Boundaries](optional-package-boundaries.md).

Use package `overview.md` pages for search-facing package summaries and task-level orientation. Use focused package docs for API, data, workflow, provider, and extension contracts.

## Package Docs By Intent

| Intent                                      | Package docs                                                                                                                                                                                                                                  |
| ------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Dashboard reporting and operations signals  | [Dashboard Reports overview](../packages/dashboard-reports/docs/overview.md), [Diagnostics overview](../packages/diagnostics/docs/overview.md), [Login Audit overview](../packages/login-audit/docs/overview.md)                              |
| Analytics, growth, and conversion reporting | [GA4 Reports overview](../packages/ga4-reports/docs/overview.md), [Insights overview](../packages/insights/docs/overview.md), [Campaign Studio overview](../packages/campaign-studio/docs/overview.md)                                        |
| SEO, search, and public discovery           | [SEO Suite overview](../packages/seo-suite/docs/overview.md), [Search overview](../packages/search/docs/overview.md), [Site Discovery README](../packages/site-discovery/README.md)                                                           |
| Demo data and frontend presentation         | [Demo Kit overview](../packages/demo-kit/docs/overview.md), [Foundation Theme overview](../packages/foundation-theme/docs/overview.md), [Creating a Capell theme](creating-a-theme.md)                                                        |
| Admin access and security                   | [Password Policy overview](../packages/password-policy/docs/overview.md), [Access Gate requests](../packages/access-gate/docs/access-requests.md), [Public Actions integrations](../packages/public-actions/docs/actions-and-integrations.md) |

| Package            | Local reference                                                                                                                                                  |
| ------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Address            | [`packages/address/README.md`](../packages/address/README.md)                                                                                                    |
| Insights           | [`packages/insights/README.md`](../packages/insights/README.md)                                                                                                  |
| AIOrchestrator     | [`packages/ai-orchestrator/README.md`](../packages/ai-orchestrator/README.md)                                                                                    |
| Login Audit        | [`packages/login-audit/README.md`](../packages/login-audit/README.md)                                                                                            |
| MigrationAssistant | [`packages/migration-assistant/README.md`](../packages/migration-assistant/README.md)                                                                            |
| Blog               | [`packages/blog/README.md`](../packages/blog/README.md)                                                                                                          |
| CampaignStudio     | [`packages/campaign-studio/README.md`](../packages/campaign-studio/README.md)                                                                                    |
| Foundation Theme   | [`packages/foundation-theme/README.md`](../packages/foundation-theme/README.md)                                                                                  |
| Deployments        | [`packages/deployments/README.md`](../packages/deployments/README.md)                                                                                            |
| Diagnostics        | [`packages/diagnostics/README.md`](../packages/diagnostics/README.md)                                                                                            |
| FormBuilder        | [`packages/form-builder/README.md`](../packages/form-builder/README.md)                                                                                          |
| Agent Bridge       | [`packages/agent-bridge/README.md`](../packages/agent-bridge/README.md)                                                                                          |
| Media Library      | [`packages/media-library/README.md`](../packages/media-library/README.md)                                                                                        |
| Navigation         | [`packages/navigation/README.md`](../packages/navigation/README.md)                                                                                              |
| Site Discovery     | [`packages/site-discovery/README.md`](../packages/site-discovery/README.md)                                                                                      |
| SEO Suite          | [`packages/seo-suite/README.md`](../packages/seo-suite/README.md)                                                                                                |
| Search             | [`packages/search/README.md`](../packages/search/README.md)                                                                                                      |
| Tags               | [`packages/tags/README.md`](../packages/tags/README.md)                                                                                                          |
| Theme Agency       | [`packages/theme-agency/README.md`](../packages/theme-agency/README.md)                                                                                          |
| Theme Corporate    | [`packages/theme-corporate/README.md`](../packages/theme-corporate/README.md)                                                                                    |
| Theme SaaS         | [`packages/theme-saas/README.md`](../packages/theme-saas/README.md)                                                                                              |
| Frontend Authoring | [`packages/frontend-authoring/README.md`](../packages/frontend-authoring/README.md), [`in-page editing`](../packages/frontend-authoring/docs/in-page-editing.md) |
| WordPress Importer | [`packages/wordpress-importer/README.md`](../packages/wordpress-importer/README.md)                                                                              |
| PublishingStudio   | [`packages/publishing-studio/README.md`](../packages/publishing-studio/README.md)                                                                                |

For the full documentation site, see [docs.capell.app](https://docs.capell.app). For the package overview and dependency matrix, see the [repository README](../README.md).

## Cross-package install order

- Blog depends on Layout Builder; install `capell-app/layout-builder` before `capell-app/blog`.
- Theme packages extend Foundation Theme; install `capell-app/layout-builder`, then `capell-app/foundation-theme`, then `capell-app/theme-agency`, `capell-app/theme-corporate`, or `capell-app/theme-saas`.
- WordPress Importer registers a source for Migration Assistant; install `capell-app/migration-assistant` before `capell-app/wordpress-importer`.

## Screenshot Automation

Package screenshots are generated from committed manifests during deployment.
See [Package Screenshot Automation](package-screenshot-automation.md) for the contract
and expected output path.
GitHub Actions provides a `Screenshot Manifests` workflow that validates the committed
manifests stay in sync.
