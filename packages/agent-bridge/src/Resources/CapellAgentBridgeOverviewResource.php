<?php

declare(strict_types=1);

namespace Capell\AgentBridge\Resources;

use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Name('capell-agent-bridge-overview')]
#[Title('Capell Agent Bridge Overview')]
#[Description('Overview of the Capell Agent Bridge two-server model and site capability workflow.')]
#[Uri('capell://agent-bridge/overview')]
#[MimeType('text/markdown')]
final class CapellAgentBridgeOverviewResource extends Resource
{
    public function handle(): Response
    {
        return Response::text(<<<'MARKDOWN'
            # Capell Agent Bridge

            Capell Agent Bridge uses two servers:

            - `CapellKnowledgeServer` is read-only and requires bearer-token authentication when exposed over HTTP.
            - `CapellSiteServer` is installed into a Capell site and requires bearer-token authentication.

            Site actions are registered capabilities. Mutating capabilities return a preview and confirmation token before execution.
            MARKDOWN);
    }
}
