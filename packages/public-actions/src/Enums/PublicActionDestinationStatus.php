<?php

declare(strict_types=1);

namespace Capell\PublicActions\Enums;

use Filament\Support\Contracts\HasLabel;

enum PublicActionDestinationStatus: string implements HasLabel
{
    case Active = 'active';
    case Paused = 'paused';

    public function getLabel(): string
    {
        return __('capell-public-actions::generic.statuses.destination.' . $this->value);
    }
}
