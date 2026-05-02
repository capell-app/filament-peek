<?php

declare(strict_types=1);

namespace Capell\Deployments\Filament\Pages;

use BackedEnum;
use Capell\Deployments\Models\DeploymentConnection;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Schema;

final class DeploymentConnectionPage extends Page
{
    protected string $view = 'capell-deployments::filament.pages.deployment-connection';

    protected static ?string $slug = 'deployment-connection';

    public static function getNavigationLabel(): string
    {
        return __('capell-deployments::plugins.deployment_connection.nav_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return (string) (__('capell-admin::navigation.group_administration'));
    }

    public static function getNavigationSort(): int
    {
        return 91;
    }

    public static function getNavigationIcon(): BackedEnum
    {
        return Heroicon::OutlinedServerStack;
    }

    public function getTitle(): string|Htmlable
    {
        return __('capell-deployments::plugins.deployment_connection.title');
    }

    /** @return array<int, DeploymentConnection> */
    public function getConnections(): array
    {
        if (! Schema::hasTable('deployment_connections')) {
            return [];
        }

        return DeploymentConnection::query()->where('is_active', true)->get()->all();
    }

    public function getGitHubOAuthUrl(): string
    {
        $raw = config('capell-deployments.oauth.github.client_id');
        $clientId = is_string($raw) ? $raw : '';

        return 'https://github.com/login/oauth/authorize?client_id=' . urlencode($clientId)
            . '&scope=repo&redirect_uri=' . urlencode(route('capell-deployments.oauth.github'));
    }

    public function getGitLabOAuthUrl(): string
    {
        $raw = config('capell-deployments.oauth.gitlab.client_id');
        $clientId = is_string($raw) ? $raw : '';

        return 'https://gitlab.com/oauth/authorize?client_id=' . urlencode($clientId)
            . '&response_type=code&scope=api&redirect_uri=' . urlencode(route('capell-deployments.oauth.gitlab'));
    }

    public function getBitbucketOAuthUrl(): string
    {
        $raw = config('capell-deployments.oauth.bitbucket.client_id');
        $clientId = is_string($raw) ? $raw : '';

        return 'https://bitbucket.org/site/oauth2/authorize?client_id=' . urlencode($clientId)
            . '&response_type=code';
    }

    public function disconnect(int $connectionId): void
    {
        if (! Schema::hasTable('deployment_connections')) {
            return;
        }

        DeploymentConnection::query()->where('id', $connectionId)->delete();

        Notification::make()
            ->title(__('capell-deployments::plugins.deployment_connection.disconnected'))
            ->success()
            ->send();
    }
}
