<?php

declare(strict_types=1);

namespace Capell\PublicActions\Enums;

use Filament\Support\Contracts\HasLabel;

enum PublicActionSubmissionStatus: string implements HasLabel
{
    case Received = 'received';
    case Handled = 'handled';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return __("capell-public-actions::generic.statuses.submission.{$this->value}");
    }
}
