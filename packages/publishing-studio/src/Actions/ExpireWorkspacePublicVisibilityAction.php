<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\Core\Models\Page;
use Capell\PublishingStudio\Exceptions\InvalidSchedulerMetadataException;
use Capell\PublishingStudio\Models\Version;
use Capell\PublishingStudio\Models\Workspace;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class ExpireWorkspacePublicVisibilityAction
{
    use AsAction;

    /**
     * @return array<class-string, array<int, int>>
     */
    public function handle(Workspace $workspace, ?CarbonImmutable $expiredAt = null): array
    {
        $expiredAt ??= CarbonImmutable::now();
        $version = $workspace->publishedVersion()->latest('published_at')->first();

        if (! $version instanceof Version) {
            return [];
        }

        $pageIds = $this->pageIds($version);

        if ($pageIds === []) {
            return [];
        }

        $homePageExists = Page::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $pageIds)
            ->homePage()
            ->exists();

        if ($homePageExists) {
            throw new InvalidSchedulerMetadataException((string) __('capell-publishing-studio::scheduler.validation.homepage_unpublish_blocked'));
        }

        DB::transaction(function () use ($pageIds, $expiredAt): void {
            Page::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $pageIds)
                ->update(['visible_until' => $expiredAt]);
        });

        $publishedModelIds = [
            Page::class => $pageIds,
        ];

        InvalidatePublishedWorkspaceFrontendCacheAction::run($publishedModelIds);

        return $publishedModelIds;
    }

    /**
     * @return array<int, int>
     */
    private function pageIds(Version $version): array
    {
        $manifest = $version->manifest;
        $pageIds = is_array($manifest) ? ($manifest[Page::class] ?? []) : [];

        return array_values(array_unique(array_map(intval(...), is_array($pageIds) ? $pageIds : [])));
    }
}
