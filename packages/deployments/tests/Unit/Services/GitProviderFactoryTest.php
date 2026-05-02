<?php

declare(strict_types=1);

use Capell\Deployments\Models\DeploymentConnection;
use Capell\Deployments\Services\GitProvider\BitbucketProvider;
use Capell\Deployments\Services\GitProvider\GitHubProvider;
use Capell\Deployments\Services\GitProvider\GitLabProvider;
use Capell\Deployments\Services\GitProvider\GitProviderFactory;

it('resolves GitHub provider', function (): void {
    $conn = DeploymentConnection::factory()->github()->make();
    expect(resolve(GitProviderFactory::class)->for($conn))->toBeInstanceOf(GitHubProvider::class);
});

it('resolves GitLab provider', function (): void {
    $conn = DeploymentConnection::factory()->gitlab()->make();
    expect(resolve(GitProviderFactory::class)->for($conn))->toBeInstanceOf(GitLabProvider::class);
});

it('resolves Bitbucket provider', function (): void {
    $conn = DeploymentConnection::factory()->bitbucket()->make();
    expect(resolve(GitProviderFactory::class)->for($conn))->toBeInstanceOf(BitbucketProvider::class);
});
