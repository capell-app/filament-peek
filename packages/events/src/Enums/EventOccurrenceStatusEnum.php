<?php

declare(strict_types=1);

namespace Capell\Events\Enums;

use Filament\Support\Contracts\HasLabel;

enum EventOccurrenceStatusEnum: string implements HasLabel
{
    case Scheduled = 'scheduled';
    case Cancelled = 'cancelled';
    case Postponed = 'postponed';
    case Full = 'full';

    public function isPubliclyBookable(): bool
    {
        return $this === self::Scheduled;
    }

    public function getLabel(): string
    {
        return __('capell-events::enum.occurrence_status_' . $this->value);
    }
}
