<?php

declare(strict_types=1);

namespace Capell\Deployments\Database\Factories;

use Capell\Deployments\Enums\GitProviderType;
use Capell\Deployments\Enums\InstallPolicy;
use Capell\Deployments\Models\DeploymentConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeploymentConnection>
 */
final class DeploymentConnectionFactory extends Factory
{
    protected $model = DeploymentConnection::class;

    public function definition(): array
    {
        return [
            'provider' => GitProviderType::GitHub,
            'repo_owner' => 'acme',
            'repo_name' => 'app',
            'default_branch' => 'main',
            'access_token_encrypted' => 'test-token',
            'refresh_token_encrypted' => null,
            'token_expires_at' => null,
            'install_policy' => InstallPolicy::PullRequestAutoMerge,
            'metadata' => null,
            'is_active' => true,
        ];
    }

    public function github(): static
    {
        return $this->state(['provider' => GitProviderType::GitHub]);
    }

    public function gitlab(): static
    {
        return $this->state(['provider' => GitProviderType::GitLab]);
    }

    public function bitbucket(): static
    {
        return $this->state(['provider' => GitProviderType::Bitbucket]);
    }
}
