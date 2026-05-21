<?php

declare(strict_types=1);

namespace Capell\Events\Policies;

final class EventPolicy extends AbstractEventResourcePolicy
{
    protected static function subject(): string
    {
        return 'Event';
    }
}
