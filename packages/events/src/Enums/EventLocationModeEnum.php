<?php

declare(strict_types=1);

namespace Capell\Events\Enums;

use Filament\Support\Contracts\HasLabel;

enum EventLocationModeEnum: string implements HasLabel
{
    case Venue = 'venue';
    case Online = 'online';
    case Hybrid = 'hybrid';
    case ToBeConfirmed = 'to_be_confirmed';

    public function getLabel(): string
    {
        return __('capell-events::enum.location_mode_' . $this->value);
    }
}
