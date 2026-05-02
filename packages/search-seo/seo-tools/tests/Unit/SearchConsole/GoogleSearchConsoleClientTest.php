<?php

declare(strict_types=1);

use Capell\Core\Models\Site;
use Capell\SeoTools\Actions\PersistSearchConsoleUrlMetricAction;
use Capell\SeoTools\Enums\SearchConsoleMetricEnum;
use Capell\SeoTools\Support\SearchConsole\GoogleSearchConsoleClient;
use Illuminate\Support\Facades\Http;

it('maps search analytics rows into page insights', function (): void {
    $credentialsPath = tempnam(sys_get_temp_dir(), 'search-console-credentials');
    $privateKey = openssl_pkey_new([
        'private_key_bits' => 1024,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    $privateKeyContents = '';

    expect($credentialsPath)->toBeString();
    expect($privateKey)->not()->toBeFalse();

    openssl_pkey_export($privateKey, $privateKeyContents);

    file_put_contents($credentialsPath, json_encode([
        'client_email' => 'seo-tools@example.iam.gserviceaccount.com',
        'private_key' => $privateKeyContents,
        'token_uri' => 'https://oauth2.googleapis.com/token',
    ], JSON_THROW_ON_ERROR));

    Http::fake([
        'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'test-token'], 200),
        'https://searchconsole.googleapis.com/*' => Http::response([
            'rows' => [[
                'clicks' => 12,
                'impressions' => 120,
                'ctr' => 0.1,
                'position' => 4.2,
            ]],
        ], 200),
    ]);

    $client = new GoogleSearchConsoleClient([
        'enabled' => true,
        'credentials_path' => $credentialsPath,
        'property_url' => 'https://example.com/',
    ]);

    $insights = $client->pageInsights('https://example.com/about');

    unlink($credentialsPath);

    expect($insights)->toHaveCount(4)
        ->and($insights[0]->metric)->toBe(SearchConsoleMetricEnum::Clicks)
        ->and($insights[0]->value)->toBe(12)
        ->and($insights[1]->metric)->toBe(SearchConsoleMetricEnum::Impressions)
        ->and($insights[1]->value)->toBe(120)
        ->and($insights[2]->metric)->toBe(SearchConsoleMetricEnum::Ctr)
        ->and($insights[2]->value)->toBe(0.1)
        ->and($insights[3]->metric)->toBe(SearchConsoleMetricEnum::Position)
        ->and($insights[3]->value)->toBe(4.2);
});

it('returns top declining pages from stored search console metrics', function (): void {
    $credentialsPath = tempnam(sys_get_temp_dir(), 'search-console-credentials');
    $site = Site::factory()->create();

    expect($credentialsPath)->toBeString();

    PersistSearchConsoleUrlMetricAction::run(
        siteId: (int) $site->getKey(),
        url: 'https://example.com/about',
        windowStart: now()->subDays(28),
        windowEnd: now(),
        clicks: 10,
        impressions: 100,
        ctr: 0.1,
        averagePosition: 4.2,
        previousClicks: 30,
        previousImpressions: 140,
        previousCtr: 0.2,
        previousAveragePosition: 3.1,
    );
    PersistSearchConsoleUrlMetricAction::run(
        siteId: (int) $site->getKey(),
        url: 'https://example.com/contact',
        windowStart: now()->subDays(28),
        windowEnd: now(),
        clicks: 40,
        impressions: 120,
        ctr: 0.3,
        averagePosition: 2.2,
        previousClicks: 30,
        previousImpressions: 110,
        previousCtr: 0.2,
        previousAveragePosition: 2.5,
    );

    $client = new GoogleSearchConsoleClient([
        'enabled' => true,
        'credentials_path' => $credentialsPath,
        'property_url' => 'https://example.com/',
    ]);

    $decliningPages = $client->decliningPages(siteId: (int) $site->getKey(), limit: 5);

    unlink($credentialsPath);

    expect($decliningPages)->toHaveCount(1)
        ->and($decliningPages[0]['url'])->toBe('https://example.com/about')
        ->and($decliningPages[0]['clicks'])->toBe(10)
        ->and($decliningPages[0]['previous_clicks'])->toBe(30)
        ->and($decliningPages[0]['click_delta'])->toBe(-20);
});
