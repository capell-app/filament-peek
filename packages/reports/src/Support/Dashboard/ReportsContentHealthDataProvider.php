<?php

declare(strict_types=1);

namespace Capell\Reports\Support\Dashboard;

use Capell\Admin\Contracts\Dashboard\ContentHealthDataProvider;
use Capell\Admin\Data\Dashboard\ContentHealthData;
use Capell\Reports\Actions\Dashboard\BuildDefaultContentHealthAction;

final class ReportsContentHealthDataProvider implements ContentHealthDataProvider
{
    public function build(): ContentHealthData
    {
        return BuildDefaultContentHealthAction::run();
    }
}
