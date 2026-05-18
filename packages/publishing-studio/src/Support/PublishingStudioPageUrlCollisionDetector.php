<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Support;

use Capell\MigrationAssistant\Contracts\PageCollisionDetector;
use Capell\MigrationAssistant\Data\PageReviewRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class PublishingStudioPageUrlCollisionDetector implements PageCollisionDetector
{
    public function detect(array $urls, ?int $resolvedSiteId): array
    {
        $hasWorkspaceId = Schema::hasColumn('page_urls', 'workspace_id');

        foreach ($urls as $urlData) {
            $url = $urlData['url'];
            $siteId = $urlData['site_id'] ?? $resolvedSiteId;
            $languageId = $urlData['language_id'] ?? null;

            $query = DB::table('page_urls')
                ->where('url', $url)
                ->whereNull('deleted_at');

            if ($siteId !== null) {
                $query->where('site_id', $siteId);
            }

            if ($languageId !== null) {
                $query->where('language_id', $languageId);
            }

            if ($hasWorkspaceId && (clone $query)->where('workspace_id', '!=', 0)->exists()) {
                return [
                    PageReviewRow::COLLISION_URL_WORKSPACE,
                    [sprintf('URL "%s" is already claimed by another workspace.', $url)],
                    PageReviewRow::ACTION_SKIP,
                ];
            }

            $liveConflict = $hasWorkspaceId
                ? (clone $query)->where('workspace_id', 0)->exists()
                : $query->exists();

            if ($liveConflict) {
                return [
                    PageReviewRow::COLLISION_URL_LIVE,
                    [sprintf('URL "%s" already exists on a live page.', $url)],
                    PageReviewRow::ACTION_UPDATE,
                ];
            }
        }

        return [PageReviewRow::COLLISION_NONE, [], PageReviewRow::ACTION_CREATE];
    }
}
