# Laravel Boost Integration

Capell MCP has two MCP surfaces:

- Laravel Boost local MCP: `php artisan boost:mcp`
- Capell Site MCP: the authenticated `mcp/capell` route registered by `capell-app/mcp`

Boost is for local development assistance. It can discover Capell package guidance from installed Composer packages and can list or preview registered Capell MCP capabilities.

The authenticated Capell Site MCP server is for site operations. It uses Capell MCP tokens, capability scopes, previews, confirmations, and audit records.

## Package Discovery

Laravel Boost discovers third-party package guidance from installed packages:

- `vendor/capell-app/*/resources/boost/guidelines`
- `vendor/capell-app/*/resources/boost/skills`

Capell packages keep these files intentionally small. They tell the agent what the package is and where to read more, usually `README.md`, `docs/`, and `src/`.

## Installing In A Host App

Install Laravel Boost and Capell MCP in the Laravel app:

```bash
composer require --dev laravel/boost
composer require capell-app/mcp:*
php artisan package:discover
```

When using zsh, quote the wildcard package constraint:

```bash
composer require 'capell-app/mcp:*'
```

Run Boost installation for the agent you use:

```bash
php artisan boost:install
```

This writes the agent MCP config for `php artisan boost:mcp`. The exact destination depends on the selected agent.

## How Capell MCP Appears In Boost

`Capell\Mcp\Providers\CapellMcpServiceProvider` checks whether Boost is installed. When `Laravel\Boost\Mcp\Boost` exists, the provider appends Capell bridge tools to:

```php
config('boost.mcp.tools.include')
```

The bridge tools are:

- `capell-list-capabilities`
- `capell-preview-capability`

These tools read from `Capell\Mcp\Support\CapellMcpCapabilityRegistry`, so package capabilities registered through `CapellMcpCapabilityProvider` become visible to Boost without each package adding its own Boost-specific tool.

## Capability Flow

For local development:

1. Agent connects to `php artisan boost:mcp`.
2. Agent calls `capell-list-capabilities`.
3. Agent calls `capell-preview-capability` for a safe preview.
4. Mutating confirmation is handled outside Boost through the authenticated Capell Site MCP server.

For authenticated site operations:

1. Create a Capell MCP token with the required scopes.
2. Connect an MCP client to the configured `mcp/capell` route.
3. Call the site capability preview tool.
4. Review the preview and confirmation token.
5. Confirm through the site MCP confirmation tool.
6. Review audit entries if needed.

## Verifying In `capell-ruby`

From the host app:

```bash
cd /Users/ben/Sites/capell-ruby
composer require 'capell-app/mcp:*' --with-all-dependencies
php artisan package:discover --ansi
find vendor/capell-app/mcp/resources/boost -maxdepth 4 -type f -print | sort
php artisan tinker --execute='var_export(config("boost.mcp.tools.include"));'
```

Expected:

- `vendor/capell-app/mcp/resources/boost/guidelines/core.blade.php`
- `vendor/capell-app/mcp/resources/boost/skills/capell-mcp-development/SKILL.md`
- `Capell\Mcp\Tools\Boost\ListBoostCapabilitiesTool`
- `Capell\Mcp\Tools\Boost\PreviewBoostCapabilityTool`

## Common Problems

- `capell-app/mcp` is not in the host app `composer.json`, so no Capell MCP provider or Boost resources are installed.
- `php artisan boost:install` has not been run, so the agent is not configured to start `boost:mcp`.
- Config is cached after changing installed packages. Run `php artisan optimize:clear`.
- A package adds an MCP capability but does not tag a `CapellMcpCapabilityProvider`, so the registry never sees it.
- A mutating operation is attempted through Boost instead of the authenticated Capell Site MCP server.
