<?php

declare(strict_types=1);

namespace Capell\Events\Enums;

use Filament\Support\Contracts\HasLabel;

enum EventBookingModeEnum: string implements HasLabel
{
    case Disabled = 'disabled';
    case External = 'external';
    case NativeRsvp = 'native_rsvp';
    case Both = 'both';

    public function allowsNativeRsvp(): bool
    {
        return in_array($this, [self::NativeRsvp, self::Both], true);
    }

    public function allowsExternalBooking(): bool
    {
        return in_array($this, [self::External, self::Both], true);
    }

    public function getLabel(): string
    {
        return __('capell-events::enum.booking_mode_' . $this->value);
    }
}
