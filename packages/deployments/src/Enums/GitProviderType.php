<?php

declare(strict_types=1);

namespace Capell\Deployments\Enums;

use Filament\Support\Contracts\HasLabel;

enum GitProviderType: string implements HasLabel
{
    case GitHub = 'github';
    case GitLab = 'gitlab';
    case Bitbucket = 'bitbucket';

    public function getLabel(): string
    {
        return match ($this) {
            self::GitHub => 'GitHub',
            self::GitLab => 'GitLab',
            self::Bitbucket => 'Bitbucket',
        };
    }
}
