<?php

declare(strict_types=1);

namespace Capell\PublicActions\Enums;

use Filament\Support\Contracts\HasLabel;

enum PublicActionIntegrationProvider: string implements HasLabel
{
    case Zapier = 'zapier';
    case Api = 'api';

    public function getLabel(): string
    {
        return __("capell-public-actions::generic.statuses.integration_provider.{$this->value}");
    }
}
