# MCP

Status: **Available, schema-owning**

This page is the consolidated implementation overview for the MCP package. It is extracted from the package README, service providers, migrations, config files, routes, resources, models, actions, and the shared Capell ERD notes where available.

## What This Plugin Adds

MCP exposes Capell knowledge and site capabilities through Laravel MCP servers with token authentication, confirmations, previews, and audit records.

- Capell knowledge and site MCP servers.
- Token, confirmation, and audit models.
- Capability registry and capability actions.
- Prompt builder Filament page.
- Tools for knowledge lookup, package recommendation, site inspection, capability listing, confirmation, and execution.

## Developer Notes

Provides a typed capability contract so MCP tools can preview, confirm, run, and audit changes instead of directly mutating site state.

- CapellMcpServiceProvider registers routes, servers, resources, and capabilities.
- Config file: capell-mcp.php.
- Routes file registers MCP endpoints through Laravel MCP.
- Middleware: AuthenticateCapellMcpToken.
- Models: CapellMcpToken, CapellMcpConfirmation, CapellMcpAuditEntry.
- Servers: CapellKnowledgeServer and CapellSiteServer.
- Laravel Boost discovers Capell package guidance from installed package `resources/boost` directories.
- When Boost is installed, CapellMcpServiceProvider appends Capell bridge tools to `boost.mcp.tools.include`.
- Boost bridge tools list and preview registered capabilities; authenticated confirmation remains on the Capell Site MCP server.

## Operational Notes

Lets trusted assistant clients inspect Capell and request controlled site operations with reviewable confirmation records.

- Adds MCP token, confirmation, and audit tables.
- Adds configurable MCP routes.
- Default config enables site route mcp/capell and disables home/knowledge route registration.
- Adds prompt builder admin page.
- Adds token prefix and auth guard configuration.

## Data And Retention

- capell_mcp_tokens stores MCP client tokens.
- capell_mcp_confirmations stores pending or completed confirmations.
- capell_mcp_audit_entries stores capability invocation records.
- Confirmation TTL defaults to 10 minutes.

## Screenshot Plan

- MCP prompt builder page.
- Token management or setup surface.
- Capability preview and confirmation flow.
- Audit entry review.
- MCP server health output.

## Pitfalls

- Enable only the MCP routes you intend to expose.
- Protect site capabilities with token auth and confirmation flow.
- Keep public_docs_paths scoped to documentation safe for MCP clients.
- Run migrations before creating tokens.
- Do not expect Boost to discover Capell MCP if the host app has not installed `capell-app/mcp`.

## Verification

- Run `vendor/bin/pest packages/mcp/tests` when package tests exist.
- Run the relevant host-app migration or package install flow in a disposable database.
- Open the listed admin or frontend surface and compare it with the screenshot plan.

## Package Manifest

- Composer name: No Composer manifest is present.
- Product group: Not declared.
- Kind: Not declared.
- Tier: Not declared.
- Bundle: Not declared.
- Contexts: Not declared.
- Requires: Not declared.
- Optional dependencies: None listed.

## Admin Surfaces

- CapellMcpPromptBuilderPage (packages/mcp/src/Filament/Pages/CapellMcpPromptBuilderPage.php, slug `capell-mcp/prompt-builder`)

## Commands

- None proven in this package directory.

## Routes And Config

- Config: packages/mcp/config/capell-mcp.php
- Route file: packages/mcp/routes/mcp.php

## Permissions And Gates

- None proven in this package directory.

## Migrations

- Migration: 2026_05_02_000001_create_capell_mcp_tokens_table.php
- Migration: 2026_05_02_000002_create_capell_mcp_confirmations_table.php
- Migration: 2026_05_02_000003_create_capell_mcp_audit_entries_table.php

## ERD Excerpt

This package has no committed ERD excerpt. Use implementation notes and extension points instead of inventing schema.

## Screenshot Automation

Deployment should read [screenshots.json](screenshots.json), install the package with demo data, resolve each admin surface or frontend URL, and write images to `public/docs/screenshots/packages/mcp`.

- MCP prompt builder page.
- Token management or setup surface.
- Capability preview and confirmation flow.
- Audit entry review.
- MCP server health output.
