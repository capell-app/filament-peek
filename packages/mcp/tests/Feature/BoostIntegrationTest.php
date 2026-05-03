<?php

declare(strict_types=1);

use Capell\Mcp\Tools\Boost\ListBoostCapabilitiesTool;
use Capell\Mcp\Tools\Boost\PreviewBoostCapabilityTool;

if (! class_exists('Laravel\\Boost\\Mcp\\Boost')) {
    eval('namespace Laravel\\Boost\\Mcp; class Boost {}');
}

it('registers Capell bridge tools with the Laravel Boost MCP server', function (): void {
    expect(config('boost.mcp.tools.include'))
        ->toContain(ListBoostCapabilitiesTool::class)
        ->toContain(PreviewBoostCapabilityTool::class);
});
