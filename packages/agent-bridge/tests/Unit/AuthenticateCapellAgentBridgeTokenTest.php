<?php

declare(strict_types=1);

use Capell\AgentBridge\Http\Middleware\AuthenticateCapellAgentBridgeToken;
use Capell\AgentBridge\Models\CapellAgentBridgeToken;
use Capell\AgentBridge\Tests\Fixtures\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

it('rejects requests without an agent bridge bearer token', function (): void {
    $response = (new AuthenticateCapellAgentBridgeToken)->handle(
        Request::create('/agent-bridge/capell'),
        fn (Request $request): never => throw new RuntimeException('Next middleware should not be called.'),
    );

    expect($response->getStatusCode())->toBe(401)
        ->and($response->getContent())->toBe('Missing Agent Bridge bearer token.')
        ->and($response->headers->get('WWW-Authenticate'))->toBe('Bearer realm="capell-agent-bridge", error="invalid_token"');
});

it('authenticates valid agent bridge bearer tokens and binds client context', function (): void {
    $user = User::query()->create([
        'name' => 'Middleware User',
        'email' => 'middleware@example.test',
        'password' => 'secret',
    ]);
    $token = new CapellAgentBridgeToken;
    $token->forceFill([
        'name' => 'Middleware token',
        'token_hash' => CapellAgentBridgeToken::hashPlainTextToken('plain-token'),
        'scopes' => ['capell.pages.read'],
    ]);
    $token->user()->associate($user);
    $token->save();

    $response = (new AuthenticateCapellAgentBridgeToken)->handle(
        Request::create('/agent-bridge/capell', server: ['HTTP_AUTHORIZATION' => 'Bearer plain-token']),
        fn (Request $request): Response => response('ok'),
    );

    expect($response->getStatusCode())->toBe(200)
        ->and($token->refresh()->last_used_at)->not->toBeNull()
        ->and(resolve(CapellAgentBridgeToken::class)->is($token))->toBeTrue();
});
