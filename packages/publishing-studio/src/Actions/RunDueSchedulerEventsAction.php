<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Enums\SchedulerEventStateEnum;
use Capell\PublishingStudio\Enums\SchedulerEventTypeEnum;
use Capell\PublishingStudio\Models\SchedulerEvent;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\Support\WorkspaceSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\Concerns\AsAction;

final class RunDueSchedulerEventsAction
{
    use AsAction;

    public function handle(int $limit = 50, bool $includePublish = true): int
    {
        $this->syncLegacyDueEvents($limit, $includePublish);

        $processedCount = 0;
        $eventTypes = [
            SchedulerEventTypeEnum::Unpublish->value,
            SchedulerEventTypeEnum::ReviewReminder->value,
        ];

        if ($includePublish) {
            $eventTypes[] = SchedulerEventTypeEnum::Publish->value;
        }

        SchedulerEvent::query()
            ->whereIn('event_type', $eventTypes)
            ->where(function (Builder $query): void {
                $query
                    ->whereIn('state', [
                        SchedulerEventStateEnum::Scheduled->value,
                        SchedulerEventStateEnum::Failed->value,
                        SchedulerEventStateEnum::SkippedEmbargo->value,
                        SchedulerEventStateEnum::SkippedReleaseWindow->value,
                    ])
                    ->orWhere(function (Builder $staleExecutingQuery): void {
                        $staleExecutingQuery
                            ->where('state', SchedulerEventStateEnum::Executing->value)
                            ->where('claimed_at', '<=', now()->subMinutes(15));
                    });
            })
            ->where('scheduled_for', '<=', now())
            ->orderBy('scheduled_for')
            ->limit($limit)
            ->get()
            ->each(function (SchedulerEvent $event) use (&$processedCount): void {
                ExecuteSchedulerEventAction::run($event);
                $processedCount++;
            });

        return $processedCount;
    }

    private function syncLegacyDueEvents(int $limit, bool $includePublish): void
    {
        if (! WorkspaceSchema::hasWorkspaceTable() || ! Schema::hasTable('publishing_scheduler_events')) {
            return;
        }

        $columns = $includePublish
            ? [
                SchedulerEventTypeEnum::Publish->value => 'publish_at',
                SchedulerEventTypeEnum::Unpublish->value => 'unpublish_at',
                SchedulerEventTypeEnum::ReviewReminder->value => 'review_reminder_at',
            ]
            : [
                SchedulerEventTypeEnum::Unpublish->value => 'unpublish_at',
                SchedulerEventTypeEnum::ReviewReminder->value => 'review_reminder_at',
            ];
        $remaining = $limit;
        $workspaceMorphClass = (new Workspace)->getMorphClass();

        foreach ($columns as $eventType => $column) {
            if ($remaining <= 0) {
                return;
            }

            $workspaces = Workspace::query()
                ->where($column, '<=', now())
                ->whereNotExists(function (QueryBuilder $query) use ($column, $eventType, $workspaceMorphClass): void {
                    $query
                        ->selectRaw('1')
                        ->from('publishing_scheduler_events')
                        ->where('publishing_scheduler_events.source_type', $workspaceMorphClass)
                        ->whereColumn('publishing_scheduler_events.source_id', 'workspaces.id')
                        ->where('publishing_scheduler_events.event_type', $eventType)
                        ->whereColumn('publishing_scheduler_events.scheduled_for', 'workspaces.' . $column)
                        ->whereNotIn('publishing_scheduler_events.state', [
                            SchedulerEventStateEnum::Scheduled->value,
                            SchedulerEventStateEnum::Failed->value,
                            SchedulerEventStateEnum::SkippedEmbargo->value,
                            SchedulerEventStateEnum::SkippedReleaseWindow->value,
                            SchedulerEventStateEnum::Executing->value,
                        ]);
                })
                ->orderBy($column)
                ->limit($remaining)
                ->get();

            $workspaces->each(function (Workspace $workspace): void {
                SyncWorkspaceSchedulerEventsAction::run($workspace);
            });

            $remaining -= $workspaces->count();
        }
    }
}
