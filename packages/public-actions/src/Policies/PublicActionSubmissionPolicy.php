<?php

declare(strict_types=1);

namespace Capell\PublicActions\Policies;

final class PublicActionSubmissionPolicy extends AbstractPublicActionResourcePolicy
{
    protected static function subject(): string
    {
        return 'PublicActionSubmission';
    }
}
