<?php

declare(strict_types=1);

namespace Capell\AgentBridge\Tools\Boost;

use Capell\AgentBridge\Data\CapabilityInvocationData;
use Capell\AgentBridge\Support\CapellAgentBridgeCapabilityRegistry;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Name('capell-preview-capability')]
#[Title('Preview Capell Capability')]
#[Description('Preview a registered Capell Agent Bridge capability from the Laravel Boost Agent Bridge server. Mutating capabilities return preview data only and must be confirmed through the authenticated Capell Site Agent Bridge server.')]
#[IsDestructive(false)]
final class PreviewBoostCapabilityTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'capability' => $schema->string()->description('Registered Capell capability key.')->required(),
            'payload' => $schema->object()->description('Capability payload.')->required(),
        ];
    }

    public function handle(Request $request, CapellAgentBridgeCapabilityRegistry $registry): ResponseFactory
    {
        $data = $request->validate([
            'capability' => ['required', 'string'],
            'payload' => ['required', 'array'],
        ]);

        $capability = $registry->get((string) $data['capability']);
        $action = resolve($capability->actionClass);
        $preview = $action->preview(new CapabilityInvocationData(
            capability: $capability,
            payload: $data['payload'],
            user: $request->user(),
        ));

        return Response::structured([
            'mode' => 'preview',
            'capability' => $capability->key,
            'preview' => $preview->toPayload(),
            'confirmation' => 'Use the authenticated Capell Site Agent Bridge server to confirm mutating capability previews.',
        ]);
    }
}
