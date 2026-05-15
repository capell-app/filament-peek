<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SchedulerEventStateEnum: string implements HasColor, HasLabel
{
    case Scheduled = 'scheduled';
    case Cancelled = 'cancelled';
    case Executing = 'executing';
    case Executed = 'executed';
    case Failed = 'failed';
    case SkippedStale = 'skipped_stale';
    case SkippedEmbargo = 'skipped_embargo';
    case SkippedReleaseWindow = 'skipped_release_window';

    public function getColor(): string
    {
        return match ($this) {
            self::Scheduled => 'info',
            self::Executing => 'warning',
            self::Executed => 'success',
            self::Cancelled => 'gray',
            self::Failed => 'danger',
            self::SkippedStale,
            self::SkippedEmbargo,
            self::SkippedReleaseWindow => 'warning',
        };
    }

    public function getLabel(): string
    {
        return (string) __('capell-publishing-studio::scheduler.states.' . $this->value);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Cancelled,
            self::Executed,
            self::Failed,
            self::SkippedStale,
            self::SkippedEmbargo,
            self::SkippedReleaseWindow,
        ], true);
    }
}
