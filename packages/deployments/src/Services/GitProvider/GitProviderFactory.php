<?php

declare(strict_types=1);

namespace Capell\Deployments\Services\GitProvider;

use Capell\Deployments\Contracts\GitProviderContract;
use Capell\Deployments\Enums\GitProviderType;
use Capell\Deployments\Models\DeploymentConnection;
use Illuminate\Contracts\Container\Container;

final class GitProviderFactory
{
    public function __construct(private readonly Container $container) {}

    public function for(DeploymentConnection $connection): GitProviderContract
    {
        return $this->container->make(match ($connection->provider) {
            GitProviderType::GitHub => GitHubProvider::class,
            GitProviderType::GitLab => GitLabProvider::class,
            GitProviderType::Bitbucket => BitbucketProvider::class,
        });
    }
}
