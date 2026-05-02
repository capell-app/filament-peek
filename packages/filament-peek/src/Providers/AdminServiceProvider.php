<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Providers;

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Capell\Core\Facades\CapellCore;
use Capell\FilamentPeek\Filament\Extenders\FilamentPeekAdminPanelExtender;
use Capell\FilamentPeek\Workspaces\WorkspacePeekPreviewActionContributor;
use Capell\Workspaces\Contracts\WorkspaceTableActionContributor;
use Illuminate\Support\ServiceProvider;

final class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! $this->isPackageInstalled()) {
            return;
        }

        $this->app->tag([FilamentPeekAdminPanelExtender::class], AdminPanelExtender::TAG);
        $this->app->tag([WorkspacePeekPreviewActionContributor::class], WorkspaceTableActionContributor::TAG);
    }

    private function isPackageInstalled(): bool
    {
        return CapellCore::isPackageInstalled(FilamentPeekServiceProvider::$packageName);
    }
}
