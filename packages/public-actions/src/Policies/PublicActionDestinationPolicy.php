<?php

declare(strict_types=1);

namespace Capell\PublicActions\Policies;

final class PublicActionDestinationPolicy extends AbstractPublicActionResourcePolicy
{
    protected static function subject(): string
    {
        return 'PublicActionDestination';
    }
}
