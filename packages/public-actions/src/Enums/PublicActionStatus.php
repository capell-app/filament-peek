<?php

declare(strict_types=1);

namespace Capell\PublicActions\Enums;

use Filament\Support\Contracts\HasLabel;

enum PublicActionStatus: string implements HasLabel
{
    case Active = 'active';
    case Paused = 'paused';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return __("capell-public-actions::generic.statuses.action.{$this->value}");
    }
}
