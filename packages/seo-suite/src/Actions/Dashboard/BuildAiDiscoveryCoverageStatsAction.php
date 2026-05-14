<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Actions\Dashboard;

use Capell\Admin\Support\SiteScope;
use Capell\SeoSuite\Models\AiDiscoveryPageProfile;
use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static array{included: int, excluded: int, missing_summary: int, stale_markdown: int} run()
 */
final class BuildAiDiscoveryCoverageStatsAction
{
    use AsAction;

    /**
     * @return array{included: int, excluded: int, missing_summary: int, stale_markdown: int}
     */
    public function handle(): array
    {
        $query = SiteScope::applyForCurrentActor(AiDiscoveryPageProfile::query(), denyWhenMissingActor: true);

        return [
            'included' => (clone $query)->where('include_in_ai_index', true)->count(),
            'excluded' => (clone $query)->where('include_in_ai_index', false)->count(),
            'missing_summary' => (clone $query)
                ->where('include_in_ai_index', true)
                ->where(fn (Builder $builder): Builder => $builder->whereNull('summary')->orWhere('summary', ''))
                ->count(),
            'stale_markdown' => (clone $query)
                ->where('include_in_ai_index', true)
                ->where(fn (Builder $builder): Builder => $builder->whereNull('last_generated_at')->orWhereNull('generated_markdown'))
                ->count(),
        ];
    }
}
