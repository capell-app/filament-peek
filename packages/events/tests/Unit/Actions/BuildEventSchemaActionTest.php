<?php

declare(strict_types=1);

use Capell\Events\Actions\BuildEventSchemaAction;
use Capell\Events\Models\Event;
use Capell\Events\Models\EventOccurrence;
use Capell\Events\Models\EventVenue;
use Carbon\CarbonImmutable;

it('builds occurrence-specific event schema', function (): void {
    $event = Event::factory()->make(['name' => 'Community lunch']);

    $venue = EventVenue::factory()->make([
        'name' => 'Town Hall',
        'line1' => '1 High Street',
        'city' => 'Leeds',
        'country' => 'United Kingdom',
    ]);

    $occurrence = EventOccurrence::factory()->make([
        'starts_at' => CarbonImmutable::parse('2026-06-01 12:00:00', 'Europe/London'),
        'ends_at' => CarbonImmutable::parse('2026-06-01 14:00:00', 'Europe/London'),
        'timezone' => 'Europe/London',
        'status' => 'scheduled',
        'location_mode' => 'venue',
        'booking_url' => 'https://example.com/book',
        'capacity' => 10,
    ]);
    $occurrence->setRelation('event', $event);
    $occurrence->setRelation('venue', $venue);

    $schema = BuildEventSchemaAction::run($occurrence);

    expect($schema['@type'])->toBe('Event')
        ->and($schema['name'])->toBe('Community lunch')
        ->and($schema['location']['name'])->toBe('Town Hall')
        ->and($schema['offers']['url'])->toBe('https://example.com/book');
});
