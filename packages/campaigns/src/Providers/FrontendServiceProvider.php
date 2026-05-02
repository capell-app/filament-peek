<?php

declare(strict_types=1);

namespace Capell\Campaigns\Providers;

use Capell\Core\Facades\CapellCore;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class FrontendServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->isPackageInstalled()) {
            return;
        }

        Blade::anonymousComponentNamespace('Capell\\Campaigns\\View\\Components');
    }

    private function isPackageInstalled(): bool
    {
        return CapellCore::isPackageInstalled(CampaignsServiceProvider::$packageName);
    }
}
