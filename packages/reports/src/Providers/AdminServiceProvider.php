<?php

declare(strict_types=1);

namespace Capell\Reports\Providers;

use Capell\Admin\Contracts\Dashboard\ContentHealthDataProvider;
use Capell\Admin\Contracts\DashboardSettingsContributor;
use Capell\Admin\Enums\DashboardEnum;
use Capell\Admin\Facades\CapellAdmin;
use Capell\Admin\Support\Dashboard\NullContentHealthDataProvider;
use Capell\Core\Facades\CapellCore;
use Capell\Reports\Filament\Settings\Contributors\ReportsDashboardSettingsContributor;
use Capell\Reports\Filament\Widgets\ContentHealthWidget;
use Capell\Reports\Filament\Widgets\PublishingTrendChartWidget;
use Capell\Reports\Support\Dashboard\ReportsContentHealthDataProvider;
use Illuminate\Support\ServiceProvider;

final class AdminServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->isPackageInstalled()) {
            return;
        }

        $this
            ->registerDashboardDataProviders()
            ->registerDashboardSettingsContributor()
            ->registerDashboardWidgets();
    }

    private function isPackageInstalled(): bool
    {
        return CapellCore::isPackageInstalled(ReportsServiceProvider::$packageName);
    }

    private function registerDashboardDataProviders(): self
    {
        if (! $this->app->bound(ContentHealthDataProvider::class)) {
            $this->app->singleton(ContentHealthDataProvider::class, ReportsContentHealthDataProvider::class);

            return $this;
        }

        $contentHealthDataProvider = $this->app->make(ContentHealthDataProvider::class);

        if ($contentHealthDataProvider instanceof NullContentHealthDataProvider) {
            $this->app->forgetInstance(ContentHealthDataProvider::class);
            $this->app->singleton(ContentHealthDataProvider::class, ReportsContentHealthDataProvider::class);
        }

        return $this;
    }

    private function registerDashboardSettingsContributor(): self
    {
        $this->app->tag([ReportsDashboardSettingsContributor::class], DashboardSettingsContributor::TAG);

        return $this;
    }

    private function registerDashboardWidgets(): self
    {
        CapellAdmin::registerDashboardWidget(PublishingTrendChartWidget::class, DashboardEnum::Main);
        CapellAdmin::registerDashboardWidget(ContentHealthWidget::class, DashboardEnum::Main);

        return $this;
    }
}
