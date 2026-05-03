<?php

declare(strict_types=1);

namespace Capell\Workspaces\Support\Dashboard;

use Capell\Admin\Contracts\Dashboard\SiteStatsDataProvider;
use Capell\Admin\Data\Dashboard\SiteStatsData;
use Capell\Workspaces\Actions\Dashboard\BuildSiteStatsAction;

final class WorkspaceSiteStatsDataProvider implements SiteStatsDataProvider
{
    public function build(string $period): SiteStatsData
    {
        return BuildSiteStatsAction::run($period);
    }
}
