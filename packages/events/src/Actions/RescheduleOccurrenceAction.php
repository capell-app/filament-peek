<?php

declare(strict_types=1);

namespace Capell\Events\Actions;

use Capell\Events\Models\EventOccurrence;
use Carbon\CarbonImmutable;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static EventOccurrence run(EventOccurrence $occurrence, CarbonImmutable $startsAt, ?CarbonImmutable $endsAt = null)
 */
class RescheduleOccurrenceAction
{
    use AsAction;

    public function handle(EventOccurrence $occurrence, CarbonImmutable $startsAt, ?CarbonImmutable $endsAt = null): EventOccurrence
    {
        $occurrence->forceFill([
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_override' => true,
            'override_data' => array_merge($occurrence->override_data ?? [], [
                'rescheduled_at' => now()->toISOString(),
            ]),
        ])->save();

        return $occurrence->refresh();
    }
}
