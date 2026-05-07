<?php

declare(strict_types=1);

use Capell\Events\Actions\RegisterForEventOccurrenceAction;
use Capell\Events\Data\EventRegistrationData;
use Capell\Events\Enums\EventBookingModeEnum;
use Capell\Events\Enums\EventRegistrationStatusEnum;
use Capell\Events\Models\EventOccurrence;
use Illuminate\Support\Facades\Notification;

it('waitlists registrations once an occurrence reaches capacity', function (): void {
    Notification::fake();

    $occurrence = EventOccurrence::factory()->create([
        'booking_mode' => EventBookingModeEnum::NativeRsvp,
        'capacity' => 1,
        'waitlist_enabled' => true,
    ]);

    $firstRegistration = RegisterForEventOccurrenceAction::run(
        $occurrence,
        new EventRegistrationData(name: 'Alice Example', email: 'alice@example.com'),
    );

    $secondRegistration = RegisterForEventOccurrenceAction::run(
        $occurrence->refresh(),
        new EventRegistrationData(name: 'Bob Example', email: 'bob@example.com'),
    );

    expect($firstRegistration->status)->toBe(EventRegistrationStatusEnum::Pending)
        ->and($secondRegistration->status)->toBe(EventRegistrationStatusEnum::Waitlisted)
        ->and($secondRegistration->waitlist_position)->toBe(1);
});
