<?php

declare(strict_types=1);

namespace Capell\Newsletter\Enums;

use Filament\Support\Contracts\HasLabel;

enum ConfirmationMode: string implements HasLabel
{
    case CapellOwned = 'capell_owned';
    case ProviderOwned = 'provider_owned';

    public function getLabel(): string
    {
        return __('capell-newsletter::generic.confirmation_mode.' . $this->value);
    }
}
