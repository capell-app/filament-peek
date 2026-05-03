<?php

declare(strict_types=1);

use Capell\Admin\Contracts\Dashboard\ContentHealthDataProvider;
use Capell\Admin\Data\Dashboard\ContentHealthData;
use Capell\Admin\Enums\DashboardEnum;
use Capell\Admin\Facades\CapellAdmin;
use Capell\Reports\Filament\Widgets\ContentHealthWidget;
use Capell\Reports\Filament\Widgets\PublishingTrendChartWidget;
use Capell\Reports\Providers\AdminServiceProvider;
use Capell\Reports\Support\Dashboard\ReportsContentHealthDataProvider;
use Capell\Reports\Tests\ReportsTestCase;

uses(ReportsTestCase::class);

it('registers reports dashboard widgets on the main dashboard', function (): void {
    expect(CapellAdmin::getDashboardWidgets(DashboardEnum::Main))
        ->toContain(PublishingTrendChartWidget::class)
        ->toContain(ContentHealthWidget::class);
});

it('binds reports content health as the installed content health provider', function (): void {
    expect(resolve(ContentHealthDataProvider::class))
        ->toBeInstanceOf(ReportsContentHealthDataProvider::class);
});

it('does not replace another package content health provider', function (): void {
    $externalContentHealthDataProvider = new class implements ContentHealthDataProvider
    {
        public function build(): ContentHealthData
        {
            return ContentHealthData::from([
                'missingMetaDescriptionCount' => 0,
                'duplicateTitleCount' => 0,
                'staleContentCount' => 0,
                'emptyContentCount' => 0,
            ]);
        }
    };

    app()->instance(ContentHealthDataProvider::class, $externalContentHealthDataProvider);

    $method = new ReflectionMethod(AdminServiceProvider::class, 'registerDashboardDataProviders');
    $method->invoke(new AdminServiceProvider(app()));

    expect(resolve(ContentHealthDataProvider::class))->toBe($externalContentHealthDataProvider);
});

it('uses reports-owned translations and views for reports widgets', function (): void {
    $contentHealthWidget = new ContentHealthWidget;
    $contentHealthView = (fn (): string => $this->view)->call($contentHealthWidget);

    expect((new PublishingTrendChartWidget)->getHeading())->toBe(__('capell-reports::dashboard.widget_publishing_trend'))
        ->and($contentHealthView)->toBe('capell-reports::widgets.content-health');
});
