<?php

declare(strict_types=1);

namespace Capell\Events\Database\Factories;

use Capell\Events\Enums\EventRegistrationStatusEnum;
use Capell\Events\Models\EventOccurrence;
use Capell\Events\Models\EventRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventRegistration>
 */
class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    public function definition(): array
    {
        return [
            'event_occurrence_id' => EventOccurrence::factory(),
            'status' => EventRegistrationStatusEnum::Pending->value,
            'name' => 'Attendee Example',
            'email' => 'attendee@example.com',
            'quantity' => 1,
            'registered_at' => now(),
        ];
    }
}
