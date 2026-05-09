<?php

declare(strict_types=1);

namespace Capell\PublicActions\Enums;

use Filament\Support\Contracts\HasLabel;

enum PublicActionDispatchStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Retryable = 'retryable';

    public function getLabel(): string
    {
        return __("capell-public-actions::generic.statuses.dispatch.{$this->value}");
    }
}
