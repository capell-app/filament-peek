<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Tests\Fixtures\Autoload;

use Capell\PublishingStudio\Contracts\ReleaseWorkspaceItemContributor;
use Capell\PublishingStudio\Models\Workspace;

final class ReleaseWorkspaceItemContributorFixture implements ReleaseWorkspaceItemContributor
{
    public function itemsFor(Workspace $workspace): array
    {
        return [];
    }
}
