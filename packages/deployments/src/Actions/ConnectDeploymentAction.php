<?php

declare(strict_types=1);

namespace Capell\Deployments\Actions;

use Capell\Deployments\Enums\GitProviderType;
use Capell\Deployments\Enums\InstallPolicy;
use Capell\Deployments\Models\DeploymentConnection;
use Lorisleiva\Actions\Concerns\AsAction;

final class ConnectDeploymentAction
{
    use AsAction;

    public function handle(
        GitProviderType $provider,
        string $repoOwner,
        string $repoName,
        string $accessToken,
        ?string $refreshToken = null,
        ?string $defaultBranch = 'main',
    ): DeploymentConnection {
        return DeploymentConnection::query()->updateOrCreate([
            'provider' => $provider->value,
            'repo_owner' => $repoOwner,
            'repo_name' => $repoName,
        ], [
            'access_token_encrypted' => $accessToken,
            'refresh_token_encrypted' => $refreshToken,
            'default_branch' => $defaultBranch ?? 'main',
            'install_policy' => InstallPolicy::PullRequestAutoMerge,
            'is_active' => true,
        ]);
    }
}
