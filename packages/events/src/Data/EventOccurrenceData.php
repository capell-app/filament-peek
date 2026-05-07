<?php

declare(strict_types=1);

namespace Capell\Events\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class EventOccurrenceData extends Data
{
    public function __construct(
        public string $occurrenceKey,
        public CarbonImmutable $startsAt,
        public ?CarbonImmutable $endsAt,
    ) {}
}
