<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Enums;

use Capell\CampaignStudio\Filament\Configurators\Elements\CampaignCtaBlockElementConfigurator;
use Capell\CampaignStudio\Filament\Configurators\Elements\CampaignHeroElementConfigurator;
use Capell\CampaignStudio\Filament\Configurators\Elements\CampaignLeadFormElementConfigurator;

enum CampaignElementConfiguratorEnum: string
{
    case CampaignHero = CampaignHeroElementConfigurator::class;
    case CampaignCtaBlock = CampaignCtaBlockElementConfigurator::class;
    case CampaignLeadForm = CampaignLeadFormElementConfigurator::class;
}
