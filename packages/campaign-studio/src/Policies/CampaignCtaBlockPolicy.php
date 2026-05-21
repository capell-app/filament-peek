<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Policies;

final class CampaignCtaBlockPolicy extends AbstractCampaignStudioResourcePolicy
{
    protected static function subject(): string
    {
        return 'CampaignCtaBlock';
    }
}
