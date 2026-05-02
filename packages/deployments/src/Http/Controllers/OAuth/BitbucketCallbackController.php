<?php

declare(strict_types=1);

namespace Capell\Deployments\Http\Controllers\OAuth;

use Capell\Deployments\Actions\ConnectDeploymentAction;
use Capell\Deployments\Enums\GitProviderType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class BitbucketCallbackController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $code = $request->query('code');
        if (! is_string($code) || $code === '') {
            return back()->withErrors(['OAuth error: missing code parameter.']);
        }

        $clientId = config('capell-deployments.oauth.bitbucket.client_id');
        $clientSecret = config('capell-deployments.oauth.bitbucket.client_secret');

        $tokenResponse = Http::withBasicAuth((string) $clientId, (string) $clientSecret)
            ->asForm()
            ->post('https://bitbucket.org/site/oauth2/access_token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
            ])
            ->json();

        $accessToken = $tokenResponse['access_token'] ?? null;
        $refreshToken = $tokenResponse['refresh_token'] ?? null;
        if (! is_string($accessToken) || $accessToken === '') {
            Log::warning('capell-deployments: Bitbucket OAuth token exchange failed', $tokenResponse);

            return back()->withErrors(['Bitbucket OAuth failed.']);
        }

        $userResponse = Http::withToken($accessToken)
            ->get('https://api.bitbucket.org/2.0/user')
            ->json();

        $username = $userResponse['username'] ?? null;
        if (! is_string($username) || $username === '') {
            return back()->withErrors(['Could not fetch Bitbucket user info.']);
        }

        ConnectDeploymentAction::run(
            provider: GitProviderType::Bitbucket,
            repoOwner: $username,
            repoName: 'app',
            accessToken: $accessToken,
            refreshToken: is_string($refreshToken) ? $refreshToken : null,
        );

        return to_route('filament.admin.pages.deployment-connection')
            ->with('status', 'Bitbucket connected successfully. Please select your repository.');
    }
}
