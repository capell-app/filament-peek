<?php

declare(strict_types=1);

namespace Capell\Newsletter\Enums;

use Filament\Support\Contracts\HasLabel;

enum SegmentType: string implements HasLabel
{
    case Static = 'static';
    case SavedFilter = 'saved_filter';

    public function getLabel(): string
    {
        return __('capell-newsletter::generic.segment_type.' . $this->value);
    }
}
