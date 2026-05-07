<?php

declare(strict_types=1);

use Capell\Events\Actions\ExpandEventRecurrenceAction;
use Capell\Events\Models\Event;
use Carbon\CarbonImmutable;

it('expands practical RRULE occurrences inside a range', function (): void {
    $event = Event::factory()->make([
        'name' => 'Weekly clinic',
        'starts_at' => CarbonImmutable::parse('2026-06-01 10:00:00', 'UTC'),
        'ends_at' => CarbonImmutable::parse('2026-06-01 12:00:00', 'UTC'),
        'timezone' => 'UTC',
        'recurrence_rule' => 'FREQ=WEEKLY;COUNT=3',
    ]);

    $occurrences = ExpandEventRecurrenceAction::run(
        $event,
        CarbonImmutable::parse('2026-06-01 00:00:00', 'UTC'),
        CarbonImmutable::parse('2026-06-30 23:59:59', 'UTC'),
    );

    expect($occurrences)->toHaveCount(3)
        ->and($occurrences->pluck('occurrenceKey')->all())->toBe([
            '20260601T100000',
            '20260608T100000',
            '20260615T100000',
        ]);
});
