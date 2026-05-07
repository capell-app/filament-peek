<?php

declare(strict_types=1);

namespace Capell\Newsletter\Enums;

use Filament\Support\Contracts\HasLabel;

enum AuthType: string implements HasLabel
{
    case ApiKey = 'api_key';
    case OAuth = 'oauth';

    public function getLabel(): string
    {
        return __('capell-newsletter::generic.auth_type.' . $this->value);
    }
}
