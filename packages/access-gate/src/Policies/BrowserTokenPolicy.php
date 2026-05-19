<?php

declare(strict_types=1);

namespace Capell\AccessGate\Policies;

final class BrowserTokenPolicy extends AbstractAccessGateResourcePolicy
{
    protected static function subject(): string
    {
        return 'BrowserToken';
    }
}
