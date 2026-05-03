<?php

declare(strict_types=1);

it('documents Laravel Boost and Capell MCP setup paths', function (): void {
    $documentation = file_get_contents(__DIR__ . '/../../docs/boost-integration.md');

    expect($documentation)
        ->toContain('php artisan boost:mcp')
        ->toContain('composer require capell-app/mcp:*')
        ->toContain('vendor/capell-app/*/resources/boost/guidelines')
        ->toContain('vendor/capell-app/*/resources/boost/skills')
        ->toContain('boost.mcp.tools.include')
        ->toContain('capell-list-capabilities')
        ->toContain('capell-preview-capability')
        ->toContain('/Users/ben/Sites/capell-ruby');
});
