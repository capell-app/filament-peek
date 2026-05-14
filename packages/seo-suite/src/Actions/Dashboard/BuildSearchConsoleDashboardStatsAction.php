<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Actions\Dashboard;

use Capell\Admin\Support\SiteScope;
use Capell\SeoSuite\Data\Dashboard\SearchConsoleDashboardStatsData;
use Capell\SeoSuite\Models\SearchConsoleUrlMetric;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
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

        $aggregate = (clone $query)
            ->selectRaw('COALESCE(SUM(clicks), 0) as clicks_total')
            ->selectRaw('COALESCE(SUM(impressions), 0) as impressions_total')
            ->selectRaw('COALESCE(SUM(CASE WHEN average_position IS NOT NULL THEN average_position * impressions ELSE 0 END), 0) as weighted_position_total')
            ->selectRaw('COALESCE(SUM(CASE WHEN average_position IS NOT NULL THEN impressions ELSE 0 END), 0) as weighted_position_impressions')
            ->selectRaw('COALESCE(SUM(CASE WHEN click_delta > 0 THEN 1 ELSE 0 END), 0) as rising_pages')
            ->selectRaw('COALESCE(SUM(CASE WHEN click_delta < 0 THEN 1 ELSE 0 END), 0) as declining_pages')
            ->selectRaw('MIN(window_start) as window_start_min')
            ->selectRaw('MAX(window_end) as window_end_max')
            ->first();

        $clicks = (int) ($aggregate?->getAttribute('clicks_total') ?? 0);
        $impressions = (int) ($aggregate?->getAttribute('impressions_total') ?? 0);
        $weightedPositionTotal = (float) ($aggregate?->getAttribute('weighted_position_total') ?? 0);
        $weightedPositionImpressions = (int) ($aggregate?->getAttribute('weighted_position_impressions') ?? 0);
        $windowCountQuery = (clone $query)
            ->select('site_id', 'window_start', 'window_end')
            ->groupBy('site_id', 'window_start', 'window_end')
            ->toBase();
        $windowCount = DB::query()
            ->fromSub($windowCountQuery, 'search_console_windows')
            ->count();

        return new SearchConsoleDashboardStatsData(
            clicks: $clicks,
            impressions: $impressions,
            ctr: $impressions === 0 ? 0.0 : round(($clicks / $impressions) * 100, 1),
            averagePosition: $weightedPositionImpressions === 0 ? null : round($weightedPositionTotal / $weightedPositionImpressions, 1),
            risingPages: (int) ($aggregate?->getAttribute('rising_pages') ?? 0),
            decliningPages: (int) ($aggregate?->getAttribute('declining_pages') ?? 0),
            windowStart: $aggregate?->getAttribute('window_start_min') === null ? null : (string) $aggregate->getAttribute('window_start_min'),
            windowEnd: $aggregate?->getAttribute('window_end_max') === null ? null : (string) $aggregate->getAttribute('window_end_max'),
            mixedWindows: $windowCount > 1,
        );
    }

    /**
     * @return Builder<SearchConsoleUrlMetric>
     */
    private function latestWindowQuery(): Builder
    {
        /** @var Builder<SearchConsoleUrlMetric> $baseQuery */
        $baseQuery = SiteScope::applyForCurrentActor(SearchConsoleUrlMetric::query(), denyWhenMissingActor: true);

        return $baseQuery->whereNotExists(function (QueryBuilder $query): void {
            $query
                ->selectRaw('1')
                ->from('search_console_url_metrics as newer_metrics')
                ->whereColumn('newer_metrics.site_id', 'search_console_url_metrics.site_id')
                ->where(function (QueryBuilder $newerWindowQuery): void {
                    $newerWindowQuery
                        ->whereColumn('newer_metrics.window_end', '>', 'search_console_url_metrics.window_end')
                        ->orWhere(function (QueryBuilder $sameEndQuery): void {
                            $sameEndQuery
                                ->whereColumn('newer_metrics.window_end', 'search_console_url_metrics.window_end')
                                ->whereColumn('newer_metrics.window_start', '>', 'search_console_url_metrics.window_start');
                        });
                });
        });
    }
}
