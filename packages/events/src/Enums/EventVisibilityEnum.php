<?php

declare(strict_types=1);

namespace Capell\Events\Enums;

use Filament\Support\Contracts\HasLabel;

enum EventVisibilityEnum: string implements HasLabel
{
    case Public = 'public';
    case Unlisted = 'unlisted';
    case Private = 'private';

    public function getLabel(): string
    {
        return __('capell-events::enum.visibility_' . $this->value);
    }
}
