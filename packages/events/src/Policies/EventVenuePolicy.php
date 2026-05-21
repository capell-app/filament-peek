<?php

declare(strict_types=1);

namespace Capell\Events\Policies;

final class EventVenuePolicy extends AbstractEventResourcePolicy
{
    protected static function subject(): string
    {
        return 'EventVenue';
    }
}
