<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SchedulerDeliveryStateEnum: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
    case Snoozed = 'snoozed';
    case Reassigned = 'reassigned';
    case Escalated = 'escalated';
    case Completed = 'completed';

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Sent,
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Snoozed,
            self::Reassigned,
            self::Escalated => 'warning',
        };
    }

    public function getLabel(): string
    {
        return (string) __('capell-publishing-studio::scheduler.delivery_states.' . $this->value);
    }
}
