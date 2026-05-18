<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Enums;

enum CampaignBlockComponentEnum: string
{
    case CampaignHero = 'capell-campaign-studio::components.block.campaign-hero';
    case CampaignCtaBlock = 'capell-campaign-studio::components.block.campaign-cta-block';
    case CampaignLeadForm = 'capell-campaign-studio::components.block.campaign-lead-form';
}
