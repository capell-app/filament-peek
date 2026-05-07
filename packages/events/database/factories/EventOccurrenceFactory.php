<?php

declare(strict_types=1);

namespace Capell\Events\Database\Factories;

use Capell\Events\Enums\EventBookingModeEnum;
use Capell\Events\Enums\EventLocationModeEnum;
use Capell\Events\Enums\EventOccurrenceStatusEnum;
use Capell\Events\Enums\EventVisibilityEnum;
use Capell\Events\Models\Event;
use Capell\Events\Models\EventOccurrence;
use Capell\Events\Models\EventVenue;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventOccurrence>
 */
class EventOccurrenceFactory extends Factory
{
    protected $model = EventOccurrence::class;

    public function definition(): array
    {
        $startsAt = CarbonImmutable::now()->addWeek()->setTime(10, 0);

        return [
            'event_id' => Event::factory(),
            'event_venue_id' => EventVenue::factory(),
            'occurrence_key' => $startsAt->format('Ymd\\THis'),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHours(2),
            'timezone' => 'UTC',
            'status' => EventOccurrenceStatusEnum::Scheduled->value,
            'visibility' => EventVisibilityEnum::Public->value,
            'location_mode' => EventLocationModeEnum::Venue->value,
            'booking_mode' => EventBookingModeEnum::NativeRsvp->value,
            'capacity' => 20,
            'waitlist_enabled' => true,
        ];
    }
}
