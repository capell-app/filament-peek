<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Actions\Dashboard;

use Capell\Admin\Support\SiteScope;
use Capell\SeoSuite\Models\SearchConsoleUrlMetric;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static Collection<int, array{id: string, url: string, clicks: int, impressions: int, ctr: float, average_position: string, click_delta: int, direction: string}> run(string $mode, int $limit = 5)
 */
final class BuildSearchConsolePageRowsAction
{
    use AsAction;

    /**
     * @return Collection<int, array{id: string, url: string, clicks: int, impressions: int, ctr: float, average_position: string, click_delta: int, direction: string}>
     */
    public function handle(string $mode, int $limit = 5): Collection
    {
        if ($mode === 'movement') {
            return $this->movementRows($limit);
        }

        $query = $this->latestWindowQuery();

        match ($mode) {
            'rising' => $query->where('click_delta', '>', 0)->orderByDesc('click_delta'),
            'declining' => $query->where('click_delta', '<', 0)->orderBy('click_delta'),
            default => $query->orderByDesc('clicks')->orderByDesc('impressions'),
        };

        return $query
            ->limit($limit)
            ->get(['id', 'url', 'clicks', 'impressions', 'ctr', 'average_position', 'click_delta'])
            ->map(fn (SearchConsoleUrlMetric $metric): array => $this->row($metric))
            ->values();
    }

    /**
     * @return Collection<int, array{id: string, url: string, clicks: int, impressions: int, ctr: float, average_position: string, click_delta: int, direction: string}>
     */
    private function movementRows(int $limit): Collection
    {
        $decliningLimit = $limit <= 0 ? 0 : max(1, intdiv($limit, 2));
        $risingLimit = $limit - $decliningLimit;

        $decliningRows = $decliningLimit === 0
            ? collect()
            : $this->latestWindowQuery()
                ->where('click_delta', '<', 0)
                ->orderBy('click_delta')
                ->limit($decliningLimit)
                ->get(['id', 'url', 'clicks', 'impressions', 'ctr', 'average_position', 'click_delta']);

        $risingRows = $risingLimit === 0
            ? collect()
            : $this->latestWindowQuery()
                ->where('click_delta', '>', 0)
                ->orderByDesc('click_delta')
                ->limit($risingLimit)
                ->get(['id', 'url', 'clicks', 'impressions', 'ctr', 'average_position', 'click_delta']);

        return $decliningRows
            ->merge($risingRows)
            ->take($limit)
            ->map(fn (SearchConsoleUrlMetric $metric): array => $this->row($metric))
            ->values();
    }

    /**
     * @return array{id: string, url: string, clicks: int, impressions: int, ctr: float, average_position: string, click_delta: int, direction: string}
     */
    private function row(SearchConsoleUrlMetric $metric): array
    {
        return [
            'id' => 'search-console-url-' . $metric->id,
            'url' => $metric->url,
            'clicks' => (int) $metric->clicks,
            'impressions' => (int) $metric->impressions,
            'ctr' => round(((float) $metric->ctr) * 100, 1),
            'average_position' => $metric->average_position === null ? __('capell-seo-suite::dashboard.not_available') : number_format((float) $metric->average_position, 1),
            'click_delta' => (int) $metric->click_delta,
            'direction' => $this->directionLabel((int) $metric->click_delta),
        ];
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

    private function directionLabel(int $clickDelta): string
    {
        if ($clickDelta > 0) {
            return __('capell-seo-suite::dashboard.gaining');
        }

        if ($clickDelta < 0) {
            return __('capell-seo-suite::dashboard.losing');
        }

        return __('capell-seo-suite::dashboard.unchanged');
    }
}
