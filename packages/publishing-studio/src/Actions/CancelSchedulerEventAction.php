<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Enums\SchedulerEventStateEnum;
use Capell\PublishingStudio\Enums\SchedulerEventTypeEnum;
use Capell\PublishingStudio\Models\SchedulerEvent;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\SchedulePublishAction;
use Illuminate\Contracts\Auth\Authenticatable;
use Lorisleiva\Actions\Concerns\AsAction;

final class CancelSchedulerEventAction
{
    use AsAction;

    public function handle(SchedulerEvent $event, ?Authenticatable $actor = null): void
    {
        $workspace = $event->workspace;

        if ($workspace instanceof Workspace) {
            match ($event->event_type) {
                SchedulerEventTypeEnum::Publish => (new SchedulePublishAction)->unschedule($workspace, $actor),
                SchedulerEventTypeEnum::Unpublish => $this->clearWorkspaceDate($workspace, 'unpublish_at', $actor),
                SchedulerEventTypeEnum::Embargo => $this->clearWorkspaceDate($workspace, 'embargo_until', $actor),
                SchedulerEventTypeEnum::ReviewReminder => $this->clearWorkspaceDate($workspace, 'review_reminder_at', $actor),
            };
        }

        $event->state = SchedulerEventStateEnum::Cancelled;
        $event->claimed_at = null;
        $event->skipped_reason = 'cancelled_by_editor';
        $event->save();
    }

    private function clearWorkspaceDate(Workspace $workspace, string $field, ?Authenticatable $actor): void
    {
        $workspace->setAttribute($field, null);
        $workspace->save();

        SyncWorkspaceSchedulerEventsAction::run($workspace, actor: $actor);
    }
}
