<?php

declare(strict_types=1);

namespace Capell\Newsletter\Actions;

use Capell\Newsletter\Enums\SyncStatus;
use Capell\Newsletter\Jobs\SyncSubscriberToProviderJob;
use Capell\Newsletter\Models\SyncAttempt;
use Lorisleiva\Actions\Concerns\AsAction;

class RequeueDueProviderSyncAttemptsAction
{
    use AsAction;

    public function handle(?int $limit = null, bool $dispatchJobs = true): int
    {
        $remaining = is_int($limit) && $limit > 0 ? $limit : null;
        $count = 0;

        while ($remaining === null || $remaining > 0) {
            $batchLimit = $remaining === null ? 500 : min(500, $remaining);
            $syncAttempts = SyncAttempt::query()
                ->where('sync_status', SyncStatus::RetryScheduled)
                ->whereNotNull('next_retry_at')
                ->where('next_retry_at', '<=', now())
                ->oldest('next_retry_at')
                ->limit($batchLimit)
                ->get();

            if ($syncAttempts->isEmpty()) {
                break;
            }

            $syncAttempts->each(function (SyncAttempt $syncAttempt) use (&$count, &$remaining, $dispatchJobs): void {
                $syncAttempt->forceFill([
                    'sync_status' => SyncStatus::Pending,
                    'next_retry_at' => null,
                ])->save();

                if ($dispatchJobs) {
                    dispatch(new SyncSubscriberToProviderJob($syncAttempt));
                }

                $count++;

                if ($remaining !== null) {
                    $remaining--;
                }
            });
        }

        return $count;
    }
}
