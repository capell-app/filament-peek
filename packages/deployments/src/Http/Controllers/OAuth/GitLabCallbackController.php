<?php

declare(strict_types=1);

namespace Capell\Deployments\Http\Controllers\OAuth;

use Capell\Deployments\Actions\ConnectDeploymentAction;
use Capell\Deployments\Enums\GitProviderType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class GitLabCallbackController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $code = $request->query('code');
        if (! is_string($code) || $code === '') {
            return back()->withErrors(['OAuth error: missing code parameter.']);
        }

        $tokenResponse = Http::post('https://gitlab.com/oauth/token', [
            'client_id' => config('capell-deployments.oauth.gitlab.client_id'),
            'client_secret' => config('capell-deployments.oauth.gitlab.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => route('capell-deployments.oauth.gitlab'),
        ])->json();

        $accessToken = $tokenResponse['access_token'] ?? null;
        $refreshToken = $tokenResponse['refresh_token'] ?? null;
        if (! is_string($accessToken) || $accessToken === '') {
            Log::warning('capell-deployments: GitLab OAuth token exchange failed', $tokenResponse);

            return back()->withErrors(['GitLab OAuth failed.']);
        }

        $userResponse = Http::withHeader('PRIVATE-TOKEN', $accessToken)
            ->get('https://gitlab.com/api/v4/user')
            ->json();

        $username = $userResponse['username'] ?? null;
        if (! is_string($username) || $username === '') {
            return back()->withErrors(['Could not fetch GitLab user info.']);
        }

        ConnectDeploymentAction::run(
            provider: GitProviderType::GitLab,
            repoOwner: $username,
            repoName: 'app',
            accessToken: $accessToken,
            refreshToken: is_string($refreshToken) ? $refreshToken : null,
        );

        return to_route('filament.admin.pages.deployment-connection')
            ->with('status', 'GitLab connected successfully. Please select your repository.');
    }
}
