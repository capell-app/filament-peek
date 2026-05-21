<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Policies;

use Illuminate\Database\Eloquent\Model;

final class CampaignLandingPagePolicy extends AbstractCampaignStudioResourcePolicy
{
    protected static function subject(): string
    {
        return 'CampaignLandingPage';
    }

    protected function recordSiteId(Model $record): ?int
    {
        $record->loadMissing('campaignGroup');

        $siteId = $record->getRelation('campaignGroup')?->getAttribute('site_id');

        return is_numeric($siteId) ? (int) $siteId : null;
    }
}
