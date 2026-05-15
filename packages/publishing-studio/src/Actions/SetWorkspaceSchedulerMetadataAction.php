<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Models\Workspace;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Lorisleiva\Actions\Concerns\AsAction;

final class SetWorkspaceSchedulerMetadataAction
{
    use AsAction;

    /**
     * @param  array{unpublish_at?: CarbonInterface|string|null, embargo_until?: CarbonInterface|string|null, review_reminder_at?: CarbonInterface|string|null}  $metadata
     */
    public function handle(Workspace $workspace, array $metadata, ?Authenticatable $actor = null): Workspace
    {
        $validated = ValidateWorkspaceSchedulerMetadataAction::run($workspace, $metadata);

        foreach ([
            'unpublish_at' => $validated->unpublishAt,
            'embargo_until' => $validated->embargoUntil,
            'review_reminder_at' => $validated->reviewReminderAt,
        ] as $field => $value) {
            if (! array_key_exists($field, $metadata)) {
                continue;
            }

            $workspace->setAttribute($field, $value);
        }

        $workspace->save();

        SyncWorkspaceSchedulerEventsAction::run($workspace, $validated, $actor);

        return $workspace->refresh();
    }
}
