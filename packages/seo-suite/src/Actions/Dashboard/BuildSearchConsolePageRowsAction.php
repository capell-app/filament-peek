<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Actions\Dashboard;

use Capell\Admin\Support\SiteScope;
use Capell\SeoSuite\Models\SearchConsoleUrlMetric;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static Collection<int, array{id: string, url: string, clicks: int, impressions: int, ctr: float, average_position: string, click_delta: int}> run(string $mode, int $limit = 5)
 */
final class BuildSearchConsolePageRowsAction
{
    use AsAction;

    /**
     * @return Collection<int, array{id: string, url: string, clicks: int, impressions: int, ctr: float, average_position: string, click_delta: int}>
     */
    public function handle(string $mode, int $limit = 5): Collection
    {
        $query = $this->latestWindowQuery();

        match ($mode) {
            'rising' => $query->where('click_delta', '>', 0)->orderByDesc('click_delta'),
            'declining' => $query->where('click_delta', '<', 0)->orderBy('click_delta'),
            'movement' => $query->where('click_delta', '!=', 0)->orderByRaw('ABS(click_delta) DESC'),
            default => $query->orderByDesc('clicks')->orderByDesc('impressions'),
        };

        return $query
            ->limit($limit)
            ->get(['id', 'url', 'clicks', 'impressions', 'ctr', 'average_position', 'click_delta'])
            ->map(fn (SearchConsoleUrlMetric $metric): array => [
                'id' => 'search-console-url-' . $metric->id,
                'url' => $metric->url,
                'clicks' => (int) $metric->clicks,
                'impressions' => (int) $metric->impressions,
                'ctr' => round(((float) $metric->ctr) * 100, 1),
                'average_position' => $metric->average_position === null ? '-' : number_format((float) $metric->average_position, 1),
                'click_delta' => (int) $metric->click_delta,
            ])
            ->values();
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
