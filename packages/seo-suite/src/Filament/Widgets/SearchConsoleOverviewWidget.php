<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Filament\Widgets;

use Capell\Admin\Contracts\CapellWidgetContract;
use Capell\Admin\Filament\Concerns\GatedByRoleAndSettings;
use Capell\SeoSuite\Actions\Dashboard\BuildSearchConsoleDashboardStatsAction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class SearchConsoleOverviewWidget extends StatsOverviewWidget implements CapellWidgetContract
{
    use GatedByRoleAndSettings;

    /** @var list<string> */
    protected static array $rolesConfigKeys = ['admin', 'super_admin'];

    protected static string $settingsKey = 'seo_search_console_overview';

    /** @var int|string|array<string, int|string|null> */
    protected int|string|array $columnSpan = ['default' => 'full'];

    protected static ?int $sort = 40;

    protected function getStats(): array
    {
        $stats = BuildSearchConsoleDashboardStatsAction::run();

        $statsCollection = [
            Stat::make(__('capell-seo-suite::dashboard.clicks'), number_format($stats->clicks)),
            Stat::make(__('capell-seo-suite::dashboard.impressions'), number_format($stats->impressions)),
            Stat::make(__('capell-seo-suite::dashboard.ctr'), number_format($stats->ctr, 1) . '%'),
            Stat::make(
                __('capell-seo-suite::dashboard.average_position'),
                $stats->averagePosition === null ? __('capell-seo-suite::dashboard.not_available') : number_format($stats->averagePosition, 1),
            ),
            Stat::make(__('capell-seo-suite::dashboard.rising_pages'), number_format($stats->risingPages)),
            Stat::make(__('capell-seo-suite::dashboard.declining_pages'), number_format($stats->decliningPages)),
        ];

        return array_map(
            fn (Stat $stat): Stat => $stat->description($this->windowDescription($stats->windowStart, $stats->windowEnd, $stats->mixedWindows)),
            $statsCollection,
        );
    }

    private function windowDescription(?string $windowStart, ?string $windowEnd, bool $mixedWindows): string
    {
        if ($windowStart === null || $windowEnd === null) {
            return __('capell-seo-suite::dashboard.no_search_console_data');
        }

        if ($mixedWindows) {
            return __('capell-seo-suite::dashboard.mixed_search_console_windows', [
                'start' => $windowStart,
                'end' => $windowEnd,
            ]);
        }

        return __('capell-seo-suite::dashboard.latest_search_console_window', [
            'start' => $windowStart,
            'end' => $windowEnd,
        ]);
    }
}
