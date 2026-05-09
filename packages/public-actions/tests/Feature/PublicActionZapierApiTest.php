<?php

declare(strict_types=1);

use Capell\PublicActions\Actions\CreatePublicActionIntegrationTokenAction;
use Capell\PublicActions\Enums\PublicActionIntegrationTokenAbility;
use Capell\PublicActions\Models\PublicAction;
use Capell\PublicActions\Models\PublicActionSubmission;

it('authenticates Zapier API tokens and lists exposed actions', function (): void {
    $created = CreatePublicActionIntegrationTokenAction::run('Zapier');

    PublicAction::factory()->create([
        'key' => 'zapier-action',
        'name' => 'Zapier action',
        'handler_key' => 'test.handler',
        'settings' => ['zapier_enabled' => true],
    ]);

    $this
        ->withToken($created->plainTextToken)
        ->getJson('/api/public-actions/zapier/actions')
        ->assertOk()
        ->assertJsonPath('actions.0.key', 'zapier-action');
});

it('rejects revoked or missing Zapier API tokens', function (): void {
    $this->getJson('/api/public-actions/zapier/me')->assertUnauthorized();

    $created = CreatePublicActionIntegrationTokenAction::run('Zapier');
    $created->token->forceFill(['revoked_at' => now()])->save();

    $this
        ->withToken($created->plainTextToken)
        ->getJson('/api/public-actions/zapier/me')
        ->assertUnauthorized();
});

it('submits public actions from Zapier and exposes sanitized submissions', function (): void {
    $created = CreatePublicActionIntegrationTokenAction::run('Zapier');

    PublicAction::factory()->create([
        'key' => 'submit-from-zapier',
        'handler_key' => 'test.handler',
        'settings' => ['zapier_enabled' => true],
    ]);

    $this
        ->withToken($created->plainTextToken)
        ->postJson('/api/public-actions/zapier/actions/submit-from-zapier/submissions', [
            'email' => 'person@example.test',
        ])
        ->assertOk()
        ->assertJsonPath('success', true);

    $this
        ->withToken($created->plainTextToken)
        ->getJson('/api/public-actions/zapier/submissions')
        ->assertOk()
        ->assertJsonPath('submissions.0.action_key', 'submit-from-zapier')
        ->assertJsonPath('submissions.0.payload.email', 'person@example.test')
        ->assertJsonMissingPath('submissions.0.metadata.ip');
});

it('enforces Zapier token abilities', function (): void {
    $created = CreatePublicActionIntegrationTokenAction::run(
        name: 'Read only',
        abilities: [PublicActionIntegrationTokenAbility::ReadSubmissions],
    );

    PublicActionSubmission::factory()->create();

    $this
        ->withToken($created->plainTextToken)
        ->getJson('/api/public-actions/zapier/actions')
        ->assertForbidden();

    $this
        ->withToken($created->plainTextToken)
        ->getJson('/api/public-actions/zapier/submissions')
        ->assertOk();
});
