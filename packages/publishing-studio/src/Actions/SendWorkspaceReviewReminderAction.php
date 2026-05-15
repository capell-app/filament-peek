<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Enums\SchedulerDeliveryStateEnum;
use Capell\PublishingStudio\Enums\WorkspaceStatusEnum;
use Capell\PublishingStudio\Models\SchedulerDelivery;
use Capell\PublishingStudio\Models\SchedulerEvent;
use Capell\PublishingStudio\Models\WorkspaceReviewAssignment;
use Capell\PublishingStudio\Notifications\WorkspaceReviewReminderNotification;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

final class SendWorkspaceReviewReminderAction
{
    use AsAction;

    public function handle(SchedulerEvent $event): int
    {
        $workspace = $event->workspace;

        if ($workspace === null || ! in_array($workspace->status, [WorkspaceStatusEnum::InReview, WorkspaceStatusEnum::Approved, WorkspaceStatusEnum::Scheduled], true)) {
            return 0;
        }

        $sentCount = 0;

        $workspace->reviewAssignments()
            ->whereNull('decision')
            ->with('reviewer')
            ->get()
            ->each(function (WorkspaceReviewAssignment $assignment) use ($event, $workspace, &$sentCount): void {
                $recipient = $assignment->reviewer;

                if ($recipient === null) {
                    return;
                }

                $delivery = SchedulerDelivery::query()->firstOrCreate(
                    [
                        'dedupe_key' => implode(':', [
                            (string) $event->id,
                            $assignment->reviewer_type ?? 'reviewer',
                            (string) $assignment->reviewer_id,
                        ]),
                    ],
                    [
                        'scheduler_event_id' => $event->id,
                        'state' => SchedulerDeliveryStateEnum::Pending,
                        'recipient_type' => (string) $assignment->reviewer_type,
                        'recipient_id' => (int) $assignment->reviewer_id,
                    ],
                );

                if ($delivery->state === SchedulerDeliveryStateEnum::Sent) {
                    return;
                }

                try {
                    Notification::send($recipient, new WorkspaceReviewReminderNotification($workspace));
                    $delivery->state = SchedulerDeliveryStateEnum::Sent;
                    $delivery->sent_at = CarbonImmutable::now();
                    $delivery->failure_message = null;
                    $delivery->save();
                    $sentCount++;
                } catch (Throwable $throwable) {
                    $delivery->state = SchedulerDeliveryStateEnum::Failed;
                    $delivery->failed_at = CarbonImmutable::now();
                    $delivery->failure_message = mb_substr($throwable->getMessage(), 0, 1000);
                    $delivery->save();

                    report($throwable);
                }
            });

        return $sentCount;
    }
}
