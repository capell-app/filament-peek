---
name: capell-mcp-development
description: Use when editing Capell MCP servers, tokens, capabilities, previews, or Boost bridge tools.
---

# Capell MCP

MCP servers and capability adapters with token auth, previews, confirmations, and audit records.

## Look

- `packages/mcp/src`
- `packages/mcp/docs/boost-integration.md`
- `packages/mcp/README.md`

## Rules

- Register package operations through capability providers.
- Mutating site operations need preview, confirmation, scopes, and audit.
- Boost tools stay local-development bridges, not privileged bypasses.
- Run `vendor/bin/pest packages/mcp/tests`.
