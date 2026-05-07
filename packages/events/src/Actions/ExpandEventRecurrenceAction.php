<?php

declare(strict_types=1);

namespace Capell\Events\Actions;

use Capell\Events\Data\EventOccurrenceData;
use Capell\Events\Models\Event;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use RRule\RRule;

/**
 * @method static Collection<int, EventOccurrenceData> run(Event $event, CarbonImmutable $startsAt, CarbonImmutable $endsAt)
 */
class ExpandEventRecurrenceAction
{
    use AsAction;

    /**
     * @return Collection<int, EventOccurrenceData>
     */
    public function handle(Event $event, CarbonImmutable $startsAt, CarbonImmutable $endsAt): Collection
    {
        if (! $event->starts_at instanceof CarbonImmutable) {
            return collect();
        }

        $durationInSeconds = $this->durationInSeconds($event);

        if ($event->recurrence_rule === null || trim($event->recurrence_rule) === '') {
            if (! $this->overlaps($event->starts_at, $event->ends_at, $startsAt, $endsAt)) {
                return collect();
            }

            return collect([
                new EventOccurrenceData(
                    occurrenceKey: $this->occurrenceKey($event->starts_at, $event->timezone),
                    startsAt: $event->starts_at,
                    endsAt: $event->ends_at,
                ),
            ]);
        }

        $rule = new RRule($event->recurrence_rule, $event->starts_at->toDateTimeImmutable());

        return collect($rule->getOccurrencesBetween($startsAt->toDateTimeImmutable(), $endsAt->toDateTimeImmutable()))
            ->map(function (DateTimeInterface $occurrenceStart) use ($durationInSeconds, $event): EventOccurrenceData {
                $occurrenceStartsAt = CarbonImmutable::instance($occurrenceStart)->setTimezone($event->timezone);
                $occurrenceEndsAt = $durationInSeconds > 0 ? $occurrenceStartsAt->addSeconds($durationInSeconds) : null;

                return new EventOccurrenceData(
                    occurrenceKey: $this->occurrenceKey($occurrenceStartsAt, $event->timezone),
                    startsAt: $occurrenceStartsAt,
                    endsAt: $occurrenceEndsAt,
                );
            })
            ->values();
    }

    private function durationInSeconds(Event $event): int
    {
        if (! $event->starts_at instanceof CarbonImmutable || ! $event->ends_at instanceof CarbonImmutable) {
            return 0;
        }

        return (int) max(0, $event->starts_at->diffInSeconds($event->ends_at, false));
    }

    private function overlaps(CarbonImmutable $eventStartsAt, ?CarbonImmutable $eventEndsAt, CarbonImmutable $rangeStartsAt, CarbonImmutable $rangeEndsAt): bool
    {
        $eventEndsAt ??= $eventStartsAt;

        return $eventStartsAt->lessThanOrEqualTo($rangeEndsAt) && $eventEndsAt->greaterThanOrEqualTo($rangeStartsAt);
    }

    private function occurrenceKey(CarbonImmutable $startsAt, string $timezone): string
    {
        return $startsAt->setTimezone($timezone)->format('Ymd\\THis');
    }
}
