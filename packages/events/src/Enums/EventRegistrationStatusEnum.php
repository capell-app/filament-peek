<?php

declare(strict_types=1);

namespace Capell\Events\Enums;

use Filament\Support\Contracts\HasLabel;

enum EventRegistrationStatusEnum: string implements HasLabel
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Waitlisted = 'waitlisted';
    case Cancelled = 'cancelled';

    public function reservesCapacity(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed], true);
    }

    public function getLabel(): string
    {
        return __('capell-events::enum.registration_status_' . $this->value);
    }
}
