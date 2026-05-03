---
name: capell-mcp-development
description: Locate and work safely in capell-app/mcp. Use when changing mcp package behaviour and you need the package map, conventions, or where to read more.
---

# Capell Mcp

MCP servers and capability adapters for Capell CMS.

## Where To Look

- Package root: `vendor/capell-app/mcp`
- Source repo path: `packages/mcp`
- Start with `README.md`; use `docs/` for deeper package notes when present.
- Setup and Boost bridge notes: `vendor/capell-app/mcp/docs/boost-integration.md`
- Namespace: `Capell\Mcp\`
- Main code: `vendor/capell-app/mcp/src`

## Rules

- Use Actions for behaviour and Data objects for structured state.
- Keep UI/resource classes thin and translatable.
- Respect package boundaries and existing Capell extension points.
- Test focused changes with `vendor/bin/pest packages/mcp/tests` in the monorepo.
- For MCP-facing work, register a Capell MCP capability provider instead of adding one-off Boost-only logic.
