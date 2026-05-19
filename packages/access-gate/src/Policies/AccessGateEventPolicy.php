<?php

declare(strict_types=1);

namespace Capell\AccessGate\Policies;

final class AccessGateEventPolicy extends AbstractAccessGateResourcePolicy
{
    protected static function subject(): string
    {
        return 'AccessGateEvent';
    }
}
