<?php

declare(strict_types=1);

namespace Capell\Events\Contracts;

use Capell\Events\Models\EventOccurrence;

interface EventBookingProvider
{
    public function isAvailable(EventOccurrence $occurrence, int $quantity): bool;

    public function bookingUrl(EventOccurrence $occurrence): ?string;
}
