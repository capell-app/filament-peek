<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Actions\Dashboard;

use Capell\Admin\Support\SiteScope;
use Capell\SeoSuite\Data\Dashboard\SearchConsoleDashboardStatsData;
use Capell\SeoSuite\Models\SearchConsoleUrlMetric;
use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static SearchConsoleDashboardStatsData run()
 */
final class BuildSearchConsoleDashboardStatsAction
{
    use AsAction;

    public function handle(): SearchConsoleDashboardStatsData
    {
        $query = $this->latestWindowQuery();

        $clicks = (int) (clone $query)->sum('clicks');
        $impressions = (int) (clone $query)->sum('impressions');
        $averagePosition = (clone $query)->avg('average_position');

        return new SearchConsoleDashboardStatsData(
            clicks: $clicks,
            impressions: $impressions,
            ctr: $impressions === 0 ? 0.0 : round(($clicks / $impressions) * 100, 1),
            averagePosition: $averagePosition === null ? null : round((float) $averagePosition, 1),
            risingPages: (clone $query)->where('click_delta', '>', 0)->count(),
            decliningPages: (clone $query)->where('click_delta', '<', 0)->count(),
        );
    }

    /**
     * @return Builder<SearchConsoleUrlMetric>
     */
    private function latestWindowQuery(): Builder
    {
        /** @var Builder<SearchConsoleUrlMetric> $baseQuery */
        $baseQuery = SiteScope::applyForCurrentActor(SearchConsoleUrlMetric::query(), denyWhenMissingActor: true);

        $latestWindow = (clone $baseQuery)
            ->orderByDesc('window_end')
            ->orderByDesc('window_start')
            ->first(['window_start', 'window_end']);

        if (! $latestWindow instanceof SearchConsoleUrlMetric) {
            return $baseQuery->whereRaw('1 = 0');
        }

        return $baseQuery
            ->whereDate('window_start', $latestWindow->window_start->toDateString())
            ->whereDate('window_end', $latestWindow->window_end->toDateString());
    }
}
