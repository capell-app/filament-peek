<?php

declare(strict_types=1);

namespace Capell\AccessGate\Policies;

final class GrantPolicy extends AbstractAccessGateResourcePolicy
{
    protected static function subject(): string
    {
        return 'Grant';
    }
}
