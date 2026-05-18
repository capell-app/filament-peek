<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Support;

use Capell\MigrationAssistant\Contracts\PageImportTargetResolver;
use Capell\MigrationAssistant\Data\PageImportTargetData;
use Capell\MigrationAssistant\Models\ImportSession;
use Capell\PublishingStudio\Enums\WorkspaceKindEnum;
use Capell\PublishingStudio\Enums\WorkspaceStatusEnum;
use Capell\PublishingStudio\Filament\Resources\PublishingStudio\WorkspaceResource;
use Capell\PublishingStudio\Models\Workspace;
use Throwable;

final class PublishingStudioPageImportTargetResolver implements PageImportTargetResolver
{
    public function create(string $name): PageImportTargetData
    {
        $workspace = Workspace::query()->create([
            'name' => $name,
            'status' => WorkspaceStatusEnum::Open->value,
            'kind' => WorkspaceKindEnum::Import->value,
        ]);

        return new PageImportTargetData(
            type: 'publishing_studio_workspace',
            id: (int) $workspace->getKey(),
            label: $workspace->name,
            url: $this->workspaceUrl((int) $workspace->getKey()),
            legacyWorkspaceId: (int) $workspace->getKey(),
        );
    }

    public function resolve(ImportSession $session): PageImportTargetData
    {
        $workspaceId = $session->target_id ?? $session->getRawOriginal('workspace_id');
        if (! is_numeric($workspaceId)) {
            return new PageImportTargetData(
                type: 'publishing_studio_workspace',
                label: is_string($session->target_label) && $session->target_label !== '' ? $session->target_label : null,
                url: is_string($session->target_url) && $session->target_url !== '' ? $session->target_url : null,
            );
        }

        $workspace = Workspace::query()->find((int) $workspaceId);

        return new PageImportTargetData(
            type: 'publishing_studio_workspace',
            id: (int) $workspaceId,
            label: $workspace instanceof Workspace ? $workspace->name : $session->target_label,
            url: $this->workspaceUrl((int) $workspaceId) ?? $session->target_url,
            legacyWorkspaceId: (int) $workspaceId,
        );
    }

    private function workspaceUrl(int $workspaceId): ?string
    {
        try {
            return WorkspaceResource::getUrl('compare', [
                'record' => $workspaceId,
            ]);
        } catch (Throwable) {
            return null;
        }
    }
}
