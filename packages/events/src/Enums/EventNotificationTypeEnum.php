<?php

declare(strict_types=1);

namespace Capell\Events\Enums;

use Filament\Support\Contracts\HasLabel;

enum EventNotificationTypeEnum: string implements HasLabel
{
    case Admin = 'admin';
    case Confirmation = 'confirmation';
    case Reminder = 'reminder';
    case Cancellation = 'cancellation';
    case WaitlistPromotion = 'waitlist_promotion';

    public function getLabel(): string
    {
        return __('capell-events::enum.notification_type_' . $this->value);
    }
}
