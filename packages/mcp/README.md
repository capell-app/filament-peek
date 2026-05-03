# MCP

Status: **Available, schema-owning**

## What This Plugin Adds

MCP exposes Capell knowledge and site capabilities through Laravel MCP servers with token authentication, confirmations, previews, and audit records.

- Capell knowledge and site MCP servers.
- Token, confirmation, and audit models.
- Capability registry and capability actions.
- Prompt builder Filament page.
- Tools for knowledge lookup, package recommendation, site inspection, capability listing, confirmation, and execution.

## Why It Matters

**For developers:** Provides a typed capability contract so MCP tools can preview, confirm, run, and audit changes instead of directly mutating site state.

**For teams:** Lets trusted assistant clients inspect Capell and request controlled site operations with reviewable confirmation records.

## Screens And Workflow

Screenshots are generated from [docs/screenshots.json](docs/screenshots.json) during package deployment.

- MCP prompt builder page.
- Token management or setup surface.
- Capability preview and confirmation flow.
- Audit entry review.
- MCP server health output.

## Technical Shape

- CapellMcpServiceProvider registers routes, servers, resources, and capabilities.
- Config file: capell-mcp.php.
- Routes file registers MCP endpoints through Laravel MCP.
- Middleware: AuthenticateCapellMcpToken.
- Models: CapellMcpToken, CapellMcpConfirmation, CapellMcpAuditEntry.
- Servers: CapellKnowledgeServer and CapellSiteServer.

## Laravel Boost Integration

Capell MCP integrates with Laravel Boost when both packages are installed in the host app. Boost discovers lightweight package guidance from `vendor/capell-app/*/resources/boost/*`, while `capell-app/mcp` registers bridge tools into `boost.mcp.tools.include` so Boost can list and preview Capell MCP capabilities.

See [docs/boost-integration.md](docs/boost-integration.md) for host-app setup, `capell-ruby` verification, and the difference between Boost's local MCP server and Capell's authenticated Site MCP server.

## Data Model

- capell_mcp_tokens stores MCP client tokens.
- capell_mcp_confirmations stores pending or completed confirmations.
- capell_mcp_audit_entries stores capability invocation records.
- Confirmation TTL defaults to 10 minutes.

## Install Impact

- Adds MCP token, confirmation, and audit tables.
- Adds configurable MCP routes.
- Default config enables site route mcp/capell and disables home/knowledge route registration.
- Adds prompt builder admin page.
- Adds token prefix and auth guard configuration.

## Commands

- None proven in this package directory.

## Admin And Access

- CapellMcpPromptBuilderPage (packages/mcp/src/Filament/Pages/CapellMcpPromptBuilderPage.php, slug `capell-mcp/prompt-builder`)

- None proven in this package directory.

## Common Pitfalls

- Enable only the MCP routes you intend to expose.
- Protect site capabilities with token auth and confirmation flow.
- Keep public_docs_paths scoped to documentation safe for MCP clients.
- Run migrations before creating tokens.

## Quick Start

1. Install the package with `composer require capell-app/mcp`.
2. Run the package migrations or the Capell package installer required by the host app.
3. Open the new admin surface or integration point and verify the result.

## Next Steps

- [docs/overview.md](docs/overview.md)
- [../assistant/README.md](../assistant/README.md)
- [../developer-tools/README.md](../developer-tools/README.md)
