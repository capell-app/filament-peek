<?php

declare(strict_types=1);

namespace Capell\Diagnostics\Actions\Dashboard;

use Capell\Core\Models\ContentGraphEdge;
use Capell\Diagnostics\Data\Dashboard\ContentGraphHealthData;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsAction;

final class BuildContentGraphHealthAction
{
    use AsAction;

    public function handle(): ContentGraphHealthData
    {
        $edges = ContentGraphEdge::query()->get();

        return new ContentGraphHealthData(
            totalEdges: $edges->count(),
            staleSourceEdges: $edges
                ->filter(fn (ContentGraphEdge $edge): bool => ! $this->exists($edge->source_type, $edge->source_id))
                ->count(),
            staleTargetEdges: $edges
                ->filter(fn (ContentGraphEdge $edge): bool => ! $this->exists($edge->target_type, $edge->target_id))
                ->count(),
            highImpactTargets: ContentGraphEdge::query()
                ->selectRaw('target_type, target_id, count(*) as count')
                ->groupBy('target_type', 'target_id')
                ->havingRaw('count(*) > 0')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->map(fn (ContentGraphEdge $edge): array => [
                    'target_type' => $edge->target_type,
                    'target_id' => $edge->target_id,
                    'count' => (int) $edge->getAttribute('count'),
                ])
                ->all(),
        );
    }

    private function exists(string $modelType, int $modelId): bool
    {
        if (! is_subclass_of($modelType, Model::class)) {
            return false;
        }

        /** @var class-string<Model> $modelType */
        return $modelType::query()->whereKey($modelId)->exists();
    }
}
