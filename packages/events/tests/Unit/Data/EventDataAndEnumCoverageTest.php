<?php

declare(strict_types=1);

use Capell\Events\Data\EventOccurrenceData;
use Capell\Events\Data\EventRegistrationData;
use Capell\Events\Enums\EventBookingModeEnum;
use Capell\Events\Enums\EventLocationModeEnum;
use Capell\Events\Enums\EventNotificationTypeEnum;
use Capell\Events\Enums\EventOccurrenceStatusEnum;
use Capell\Events\Enums\EventRegistrationStatusEnum;
use Capell\Events\Enums\EventVisibilityEnum;
use Capell\Events\Enums\LivewireComponentEnum;
use Capell\Events\Enums\ResourceEnum;
use Capell\Events\Health\EventsHealthCheck;
use Carbon\CarbonImmutable;

it('maps event registration and occurrence data objects', function (): void {
    $startsAt = CarbonImmutable::parse('2026-06-01 10:00:00');
    $endsAt = CarbonImmutable::parse('2026-06-01 11:30:00');

    $occurrence = new EventOccurrenceData(
        occurrenceKey: 'event-1-2026-06-01',
        startsAt: $startsAt,
        endsAt: $endsAt,
    );
    $registration = new EventRegistrationData(
        name: 'Ada Lovelace',
        email: 'ada@example.com',
        phone: '+441234567890',
        quantity: 2,
        payload: ['company' => 'Capell'],
        formSubmissionId: 99,
    );

    expect($occurrence->occurrenceKey)->toBe('event-1-2026-06-01')
        ->and($occurrence->startsAt->equalTo($startsAt))->toBeTrue()
        ->and($occurrence->endsAt?->equalTo($endsAt))->toBeTrue()
        ->and($registration->quantity)->toBe(2)
        ->and($registration->payload)->toBe(['company' => 'Capell'])
        ->and($registration->formSubmissionId)->toBe(99);
});

it('defines event booking and status behavior', function (): void {
    expect(EventBookingModeEnum::Disabled->allowsNativeRsvp())->toBeFalse()
        ->and(EventBookingModeEnum::Disabled->allowsExternalBooking())->toBeFalse()
        ->and(EventBookingModeEnum::NativeRsvp->allowsNativeRsvp())->toBeTrue()
        ->and(EventBookingModeEnum::NativeRsvp->allowsExternalBooking())->toBeFalse()
        ->and(EventBookingModeEnum::External->allowsNativeRsvp())->toBeFalse()
        ->and(EventBookingModeEnum::External->allowsExternalBooking())->toBeTrue()
        ->and(EventBookingModeEnum::Both->allowsNativeRsvp())->toBeTrue()
        ->and(EventBookingModeEnum::Both->allowsExternalBooking())->toBeTrue()
        ->and(EventOccurrenceStatusEnum::Scheduled->isPubliclyBookable())->toBeTrue()
        ->and(EventOccurrenceStatusEnum::Cancelled->isPubliclyBookable())->toBeFalse()
        ->and(EventRegistrationStatusEnum::Pending->reservesCapacity())->toBeTrue()
        ->and(EventRegistrationStatusEnum::Confirmed->reservesCapacity())->toBeTrue()
        ->and(EventRegistrationStatusEnum::Waitlisted->reservesCapacity())->toBeFalse()
        ->and(EventRegistrationStatusEnum::Cancelled->reservesCapacity())->toBeFalse();
});

it('defines event labels, components, resources, and health metadata', function (): void {
    expect(EventsHealthCheck::compatibleCapellApiVersion())->toBe('^4.0')
        ->and(EventBookingModeEnum::Both->getLabel())->toBe('capell-events::enum.booking_mode_both')
        ->and(EventLocationModeEnum::Hybrid->getLabel())->toBe('capell-events::enum.location_mode_hybrid')
        ->and(EventNotificationTypeEnum::WaitlistPromotion->getLabel())->toBe('capell-events::enum.notification_type_waitlist_promotion')
        ->and(EventOccurrenceStatusEnum::Postponed->getLabel())->toBe('capell-events::enum.occurrence_status_postponed')
        ->and(EventRegistrationStatusEnum::Confirmed->getLabel())->toBe('capell-events::enum.registration_status_confirmed')
        ->and(EventVisibilityEnum::Unlisted->getLabel())->toBe('capell-events::enum.visibility_unlisted')
        ->and(LivewireComponentEnum::getComponents())->toHaveKeys([
            'capell-events::event-calendar',
            'capell-events::page.events-calendar',
            'capell-events::page.events-listing',
        ])
        ->and(ResourceEnum::Event->value)->toContain('EventResource')
        ->and(ResourceEnum::EventOccurrence->value)->toContain('EventOccurrenceResource');
});
