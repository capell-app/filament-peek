<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Enums;

use Capell\CampaignStudio\Filament\Configurators\Blocks\CampaignCtaBlockBlockConfigurator;
use Capell\CampaignStudio\Filament\Configurators\Blocks\CampaignHeroBlockConfigurator;
use Capell\CampaignStudio\Filament\Configurators\Blocks\CampaignLeadFormBlockConfigurator;

enum CampaignBlockConfiguratorEnum: string
{
    case CampaignHero = CampaignHeroBlockConfigurator::class;
    case CampaignCtaBlock = CampaignCtaBlockBlockConfigurator::class;
    case CampaignLeadForm = CampaignLeadFormBlockConfigurator::class;
}
