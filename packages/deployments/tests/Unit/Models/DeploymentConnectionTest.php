<?php

declare(strict_types=1);

use Capell\Deployments\Enums\GitProviderType;
use Capell\Deployments\Enums\InstallPolicy;
use Capell\Deployments\Models\DeploymentConnection;
use Illuminate\Support\Facades\DB;

it('encrypts access_token at rest', function (): void {
    $connection = DeploymentConnection::query()->create([
        'provider' => GitProviderType::GitHub,
        'repo_owner' => 'acme',
        'repo_name' => 'app',
        'access_token_encrypted' => 'plain-token-abc',
        'install_policy' => InstallPolicy::PullRequestAutoMerge,
    ]);

    expect($connection->fresh()->access_token_encrypted)->toBe('plain-token-abc');
    expect(DB::table('deployment_connections')->first()->access_token_encrypted)
        ->not->toBe('plain-token-abc');
});

it('exposes the repo coordinate as a string', function (): void {
    $connection = DeploymentConnection::factory()->github()->create([
        'repo_owner' => 'acme',
        'repo_name' => 'app',
    ]);

    expect($connection->repoCoordinate())->toBe('acme/app');
});
