<?php

declare(strict_types=1);

namespace Capell\Diagnostics\Data\Dashboard;

use Spatie\LaravelData\Data;

final class ContentGraphHealthData extends Data
{
    /**
     * @param  array<int, array{target_type: string, target_id: int, count: int}>  $highImpactTargets
     */
    public function __construct(
        public readonly int $totalEdges,
        public readonly int $staleSourceEdges,
        public readonly int $staleTargetEdges,
        public readonly array $highImpactTargets,
    ) {}
}
