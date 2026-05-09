<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Enums;

use Filament\Support\Contracts\HasLabel;

enum AiDiscoveryStatusEnum: string implements HasLabel
{
    case Enabled = 'enabled';
    case Disabled = 'disabled';
    case Fresh = 'fresh';
    case Stale = 'stale';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return __('capell-seo-suite::generic.ai_discovery_status_' . $this->value);
    }
}
