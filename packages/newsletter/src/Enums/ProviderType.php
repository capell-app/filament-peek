<?php

declare(strict_types=1);

namespace Capell\Newsletter\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProviderType: string implements HasLabel
{
    case Mailchimp = 'mailchimp';
    case Kit = 'kit';
    case CampaignMonitor = 'campaign_monitor';
    case Fake = 'fake';

    public function getLabel(): string
    {
        return __('capell-newsletter::generic.provider.' . $this->value);
    }
}
