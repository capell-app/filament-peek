<?php

declare(strict_types=1);

namespace Capell\MediaCurator\Actions\Reports;

use Capell\Admin\Support\MediaScope;
use Capell\Core\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\Concerns\AsAction;

final class BuildMediaHealthQueryAction
{
    use AsAction;

    public function handle(): Builder
    {
        $staleThreshold = now()->subDays(90);
        $mediaQuery = MediaScope::applyForCurrentActor(Media::query());

        if (! Schema::hasTable('media_usage')) {
            return $mediaQuery;
        }

        return $mediaQuery->where(function (Builder $nestedMediaQuery) use ($staleThreshold): void {
            $nestedMediaQuery
                ->whereNotIn(
                    'id',
                    fn (QueryBuilder $usageQuery): QueryBuilder => $usageQuery
                        ->select('media_id')
                        ->distinct()
                        ->from('media_usage'),
                )
                ->orWhereIn(
                    'id',
                    fn (QueryBuilder $usageQuery): QueryBuilder => $usageQuery
                        ->select('media_id')
                        ->distinct()
                        ->from('media_usage')
                        ->where(function (QueryBuilder $attributeQuery): void {
                            $attributeQuery
                                ->whereNull('custom_attributes')
                                ->orWhereNull('custom_attributes->alt');
                        }),
                )
                ->orWhere('updated_at', '<', $staleThreshold);
        });
    }
}
