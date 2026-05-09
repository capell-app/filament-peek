<?php

declare(strict_types=1);

namespace Capell\Diagnostics\Actions\Dashboard;

use Capell\Core\Models\ContentGraphEdge;
use Capell\Diagnostics\Data\Dashboard\ContentGraphHealthData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

final class BuildContentGraphHealthAction
{
    use AsAction;

    public function handle(): ContentGraphHealthData
    {
        return new ContentGraphHealthData(
            totalEdges: ContentGraphEdge::query()->count(),
            staleSourceEdges: $this->staleEdgeCount('source'),
            staleTargetEdges: $this->staleEdgeCount('target'),
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

    private function staleEdgeCount(string $side): int
    {
        $typeColumn = $side . '_type';
        $idColumn = $side . '_id';
        $staleCount = 0;

        ContentGraphEdge::query()
            ->select($typeColumn, $idColumn)
            ->distinct()
            ->orderBy($typeColumn)
            ->orderBy($idColumn)
            ->chunk(500, function (Collection $edges) use ($typeColumn, $idColumn, &$staleCount): void {
                $edges
                    ->groupBy(fn (ContentGraphEdge $edge): string => (string) $edge->getAttribute($typeColumn))
                    ->each(function (Collection $modelEdges, string $modelType) use ($idColumn, &$staleCount): void {
                        if (! is_subclass_of($modelType, Model::class)) {
                            $staleCount += $modelEdges->count();

                            return;
                        }

                        /** @var class-string<Model> $modelType */
                        $ids = $modelEdges
                            ->pluck($idColumn)
                            ->map(fn (mixed $modelId): int => (int) $modelId)
                            ->unique()
                            ->values();

                        $existingIds = $modelType::query()
                            ->whereKey($ids->all())
                            ->pluck((new $modelType)->getKeyName())
                            ->map(fn (mixed $modelId): int => (int) $modelId);

                        $staleCount += $ids->diff($existingIds)->count();
                    });
            });

        return $staleCount;
    }
}
