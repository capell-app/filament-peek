<?php

declare(strict_types=1);

namespace Capell\AccessGate\Policies;

final class ClaimTokenPolicy extends AbstractAccessGateResourcePolicy
{
    protected static function subject(): string
    {
        return 'ClaimToken';
    }
}
