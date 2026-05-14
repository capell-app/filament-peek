<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Data\Dashboard;

use Spatie\LaravelData\Data;

final class SearchConsoleDashboardStatsData extends Data
{
    public function __construct(
        public readonly int $clicks,
        public readonly int $impressions,
        public readonly float $ctr,
        public readonly ?float $averagePosition,
        public readonly int $risingPages,
        public readonly int $decliningPages,
        public readonly ?string $windowStart,
        public readonly ?string $windowEnd,
        public readonly bool $mixedWindows,
    ) {}
}
