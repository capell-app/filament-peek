<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Enums;

use Filament\Support\Contracts\HasLabel;

enum CampaignBlockComponentEnum: string implements HasLabel
{
    case CampaignHero = 'capell-campaign-studio::components.block.campaign-hero';
    case CampaignCtaBlock = 'capell-campaign-studio::components.block.campaign-cta-block';
    case CampaignLeadForm = 'capell-campaign-studio::components.block.campaign-lead-form';

    public function getLabel(): string
    {
        return __('capell-campaign-studio::generic.campaign_block_components.' . str($this->name)->snake()->toString());
    }
}
