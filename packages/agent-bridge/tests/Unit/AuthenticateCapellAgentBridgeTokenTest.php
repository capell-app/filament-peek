<?php

declare(strict_types=1);

use Capell\AgentBridge\Http\Middleware\AuthenticateCapellAgentBridgeToken;
use Illuminate\Http\Request;

it('rejects requests without an agent bridge bearer token', function (): void {
    $response = (new AuthenticateCapellAgentBridgeToken)->handle(
        Request::create('/agent-bridge/capell'),
        fn (Request $request): never => throw new RuntimeException('Next middleware should not be called.'),
    );

    expect($response->getStatusCode())->toBe(401)
        ->and($response->getContent())->toBe('Missing Agent Bridge bearer token.')
        ->and($response->headers->get('WWW-Authenticate'))->toBe('Bearer realm="capell-agent-bridge", error="invalid_token"');
});
