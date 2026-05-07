<?php

declare(strict_types=1);

namespace Capell\Events\Enums;

use Capell\Events\Filament\Resources\Events\EventResource;
use Capell\Events\Filament\Resources\Occurrences\EventOccurrenceResource;
use Capell\Events\Filament\Resources\Registrations\EventRegistrationResource;
use Capell\Events\Filament\Resources\Venues\EventVenueResource;

enum ResourceEnum: string
{
    case Event = EventResource::class;
    case EventVenue = EventVenueResource::class;
    case EventOccurrence = EventOccurrenceResource::class;
    case EventRegistration = EventRegistrationResource::class;
}
