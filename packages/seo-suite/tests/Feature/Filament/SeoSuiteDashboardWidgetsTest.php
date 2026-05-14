<?php

declare(strict_types=1);

use Capell\Admin\Contracts\DashboardSettingsContributor;
use Capell\Admin\Enums\DashboardEnum;
use Capell\Admin\Facades\CapellAdmin;
use Capell\SeoSuite\Filament\Settings\Contributors\SeoSuiteDashboardSettingsContributor;
use Capell\SeoSuite\Filament\Widgets\AiDiscoveryCoverageWidget;
use Capell\SeoSuite\Filament\Widgets\SearchConsoleOverviewWidget;
use Capell\SeoSuite\Filament\Widgets\SearchMovementWidget;
use Capell\SeoSuite\Filament\Widgets\SeoOpportunitiesWidget;
use Capell\SeoSuite\Filament\Widgets\TopSearchPagesWidget;
use Livewire\Livewire;

it('exposes SEO Suite dashboard settings keys with translated labels', function (): void {
    $entries = (new SeoSuiteDashboardSettingsContributor)->settingsKeys();

    expect(collect($entries)->pluck('key')->all())->toBe([
        'seo_search_console_overview',
        'seo_top_search_pages',
        'seo_search_movement',
        'seo_opportunities',
        'seo_ai_discovery_coverage',
    ]);

    foreach ($entries as $entry) {
        expect($entry['label'])->toBeString()->not->toBe('')
            ->and(str_contains($entry['label'], 'capell-seo-suite::'))->toBeFalse()
            ->and($entry['group'])->toBeString()->not->toBe('');
    }
});

it('registers SEO Suite dashboard widgets and settings contributor', function (): void {
    $contributors = collect(app()->tagged(DashboardSettingsContributor::TAG))
        ->map(fn (DashboardSettingsContributor $contributor): string => $contributor::class);

    expect($contributors)->toContain(SeoSuiteDashboardSettingsContributor::class)
        ->and(CapellAdmin::getDashboardWidgets(DashboardEnum::Main))
        ->toContain(SearchConsoleOverviewWidget::class)
        ->toContain(TopSearchPagesWidget::class)
        ->toContain(SearchMovementWidget::class)
        ->toContain(SeoOpportunitiesWidget::class)
        ->toContain(AiDiscoveryCoverageWidget::class);
});

it('renders SEO Suite dashboard widgets', function (string $widgetClass): void {
    Livewire::test($widgetClass)->assertOk();
})->with([
    SearchConsoleOverviewWidget::class,
    TopSearchPagesWidget::class,
    SearchMovementWidget::class,
    SeoOpportunitiesWidget::class,
    AiDiscoveryCoverageWidget::class,
]);
