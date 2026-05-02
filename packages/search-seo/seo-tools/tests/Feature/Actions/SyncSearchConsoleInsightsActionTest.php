<?php

declare(strict_types=1);

use Capell\Core\Models\Site;
use Capell\SeoTools\Actions\PersistSearchConsoleUrlMetricAction;
use Capell\SeoTools\Actions\SyncSearchConsoleInsightsAction;
use Capell\SeoTools\Contracts\SearchConsoleClientInterface;
use Capell\SeoTools\Models\SearchConsoleUrlMetric;
use Capell\SeoTools\Support\SearchConsole\NullSearchConsoleClient;

it('returns unconfigured sync results without writing metrics', function (): void {
    $site = Site::factory()->create();

    app()->instance(SearchConsoleClientInterface::class, new NullSearchConsoleClient);

    $result = SyncSearchConsoleInsightsAction::run((int) $site->getKey());

    expect($result)->toBe([
        'synced' => 0,
        'configured' => false,
        'pages' => [],
    ])->and(SearchConsoleUrlMetric::query()->count())->toBe(0);
});

it('stores and queries declining search console url metrics', function (): void {
    $site = Site::factory()->create();

    PersistSearchConsoleUrlMetricAction::run(
        siteId: (int) $site->getKey(),
        url: 'https://example.com/a',
        windowStart: now()->subDays(28),
        windowEnd: now(),
        clicks: 10,
        impressions: 100,
        ctr: 0.10,
        averagePosition: 4.2,
        previousClicks: 30,
        previousImpressions: 180,
        previousCtr: 0.16,
        previousAveragePosition: 3.1,
    );

    $metric = SearchConsoleUrlMetric::query()
        ->decliningPages((int) $site->getKey(), 10)
        ->first();

    expect($metric)->not()->toBeNull()
        ->and($metric->url)->toBe('https://example.com/a')
        ->and($metric->click_delta)->toBe(-20);
});
