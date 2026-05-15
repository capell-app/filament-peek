<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Enums;

enum CampaignElementComponentEnum: string
{
    case CampaignHero = 'capell-campaign-studio::components.element.campaign-hero';
    case CampaignCtaBlock = 'capell-campaign-studio::components.element.campaign-cta-block';
    case CampaignLeadForm = 'capell-campaign-studio::components.element.campaign-lead-form';
}
