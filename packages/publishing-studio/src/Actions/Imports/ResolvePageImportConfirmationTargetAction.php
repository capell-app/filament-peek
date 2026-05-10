<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions\Imports;

use Capell\Core\Models\Site;
use Capell\MigrationAssistant\Models\ImportSession;
use Capell\PublishingStudio\Models\Workspace;
use Lorisleiva\Actions\Concerns\AsObject;

final class ResolvePageImportConfirmationTargetAction
{
    use AsObject;

    public function handle(ImportSession $session): string
    {
        $siteIds = $this->resolvedSiteIds($session);

        if (count($siteIds) === 1) {
            $site = Site::query()->find(array_key_first($siteIds));

            if ($site instanceof Site && is_string($site->name) && $site->name !== '') {
                return $site->name;
            }
        }

        $workspace = Workspace::query()->find($session->workspace_id);

        if ($workspace instanceof Workspace && is_string($workspace->name) && $workspace->name !== '') {
            return $workspace->name;
        }

        return '';
    }

    /**
     * @return array<int, true>
     */
    private function resolvedSiteIds(ImportSession $session): array
    {
        $resolutionMap = is_array($session->resolution_map) ? $session->resolution_map : [];
        $resolved = is_array($resolutionMap['resolved'] ?? null) ? $resolutionMap['resolved'] : [];
        $siteIds = [];

        foreach ($resolved as $ref => $resolution) {
            if (! is_string($ref) || ! str_starts_with($ref, 'site:') || ! is_array($resolution)) {
                continue;
            }

            $localId = $resolution['local_id'] ?? null;

            if (is_int($localId)) {
                $siteIds[$localId] = true;
            }

            if (is_string($localId) && ctype_digit($localId)) {
                $siteIds[(int) $localId] = true;
            }
        }

        return $siteIds;
    }
}
