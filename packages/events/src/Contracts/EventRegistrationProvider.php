<?php

declare(strict_types=1);

namespace Capell\Events\Contracts;

use Capell\Events\Data\EventRegistrationData;
use Capell\Events\Models\EventOccurrence;
use Capell\Events\Models\EventRegistration;

interface EventRegistrationProvider
{
    public function register(EventOccurrence $occurrence, EventRegistrationData $registrationData): EventRegistration;
}
