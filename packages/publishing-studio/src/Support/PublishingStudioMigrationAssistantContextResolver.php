<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Support;

use Capell\Core\Models\Page;
use Capell\MigrationAssistant\Contracts\MigrationAssistantContextResolver;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\WorkspaceContext;
use Closure;

final class PublishingStudioMigrationAssistantContextResolver implements MigrationAssistantContextResolver
{
    public function wrap(Closure $callback, ?int $sourceWorkspaceId = null): mixed
    {
        if ($sourceWorkspaceId === null) {
            return WorkspaceContext::runWith(null, $callback);
        }

        $workspace = Workspace::query()->findOrFail($sourceWorkspaceId);

        return WorkspaceContext::runWith($workspace, $callback);
    }

    public function resolvePageIds(array $pageIds, ?int $sourceWorkspaceId = null): array
    {
        if ($sourceWorkspaceId === null || $pageIds === []) {
            return $pageIds;
        }

        $uuidsByPageId = Page::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $pageIds)
            ->whereNotNull('uuid')
            ->pluck('uuid', 'id');

        if ($uuidsByPageId->isEmpty()) {
            return $pageIds;
        }

        $workspaceIdsByUuid = Page::query()
            ->withoutGlobalScopes()
            ->where('workspace_id', $sourceWorkspaceId)
            ->whereIn('uuid', $uuidsByPageId->values()->all())
            ->pluck('id', 'uuid');

        return array_map(
            static fn (int|string $pageId): int|string => $workspaceIdsByUuid->get($uuidsByPageId->get($pageId), $pageId),
            $pageIds,
        );
    }
}
