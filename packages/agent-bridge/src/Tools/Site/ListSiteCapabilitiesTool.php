<?php

declare(strict_types=1);

namespace Capell\AgentBridge\Tools\Site;

use Capell\AgentBridge\Data\AuthenticatedAgentBridgeClientData;
use Capell\AgentBridge\Enums\CapabilityServerEnum;
use Capell\AgentBridge\Support\CapellAgentBridgeCapabilityRegistry;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('capell-site-list-capabilities')]
#[Title('List Site Capabilities')]
#[Description('List installed Capell Agent Bridge capabilities visible to the authenticated Agent Bridge client.')]
#[IsReadOnly]
final class ListSiteCapabilitiesTool extends Tool
{
    public function handle(CapellAgentBridgeCapabilityRegistry $registry, AuthenticatedAgentBridgeClientData $client): ResponseFactory
    {
        return Response::structured([
            'capabilities' => $registry
                ->visibleFor(CapabilityServerEnum::Site, $client->scopes)
                ->map(fn ($capability): array => $capability->toPayload())
                ->all(),
        ]);
    }
}
