<?php

declare(strict_types=1);

namespace Capell\PublishingStudio;

use Capell\PublishingStudio\Actions\RunDueSchedulerEventsAction;
use Capell\PublishingStudio\Enums\WorkspaceStatusEnum;
use Capell\PublishingStudio\Exceptions\EmbargoActiveException;
use Capell\PublishingStudio\Exceptions\ReleaseWindowClosedException;
use Capell\PublishingStudio\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Backwards-compatible scheduler entry point. The durable scheduler events
 * now own publish, public-expiry, and review reminder execution.
 */
class PublishScheduledPublishingStudioJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(?Publisher $publisher = null): void
    {
        $publisher ??= new Publisher;

        Workspace::query()
            ->where('status', WorkspaceStatusEnum::Scheduled->value)
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now())
            ->oldest('publish_at')
            ->each(function (Workspace $workspace) use ($publisher): void {
                try {
                    $publisher->publish($workspace);
                } catch (EmbargoActiveException|ReleaseWindowClosedException) {
                    // Leave Scheduled so the durable scheduler can retry once unblocked.
                } catch (Throwable $failure) {
                    report($failure);
                }
            });

        RunDueSchedulerEventsAction::run(limit: 50, includePublish: false);
    }
}
