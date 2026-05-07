<?php

declare(strict_types=1);

namespace Capell\Events\Actions;

use Capell\Events\Enums\EventOccurrenceStatusEnum;
use Capell\Events\Models\EventOccurrence;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static EventOccurrence run(EventOccurrence $occurrence, ?string $reason = null)
 */
class CancelOccurrenceAction
{
    use AsAction;

    public function handle(EventOccurrence $occurrence, ?string $reason = null): EventOccurrence
    {
        $occurrence->forceFill([
            'status' => EventOccurrenceStatusEnum::Cancelled,
            'is_override' => true,
            'override_data' => array_merge($occurrence->override_data ?? [], [
                'cancelled_at' => now()->toISOString(),
                'cancellation_reason' => $reason,
            ]),
        ])->save();

        return $occurrence->refresh();
    }
}
