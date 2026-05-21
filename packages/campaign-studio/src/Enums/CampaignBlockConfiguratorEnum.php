<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Enums;

use Capell\CampaignStudio\Filament\Configurators\Blocks\CampaignCtaBlockBlockConfigurator;
use Capell\CampaignStudio\Filament\Configurators\Blocks\CampaignHeroBlockConfigurator;
use Capell\CampaignStudio\Filament\Configurators\Blocks\CampaignLeadFormBlockConfigurator;
use Filament\Support\Contracts\HasLabel;

enum CampaignBlockConfiguratorEnum: string implements HasLabel
{
    case CampaignHero = CampaignHeroBlockConfigurator::class;
    case CampaignCtaBlock = CampaignCtaBlockBlockConfigurator::class;
    case CampaignLeadForm = CampaignLeadFormBlockConfigurator::class;

    public function getLabel(): string
    {
        return __('capell-campaign-studio::generic.campaign_block_configurators.' . str($this->name)->snake()->toString());
    }
}
