<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Tests\Fixtures\Autoload;

use Capell\PublishingStudio\Events\Contracts\WorkspaceEventSubscriber;
use Capell\PublishingStudio\Models\Workspace;

class TestSubscriber implements WorkspaceEventSubscriber
{
    public function handle(string $event, object $context): void {}

    public function beforeClone(Workspace $source, Workspace $target): bool
    {
        return true;
    }

    public function afterClone(Workspace $source, Workspace $target): void {}

    public function beforePublish(Workspace $workspace): bool
    {
        return true;
    }

    public function afterPublish(Workspace $workspace): void {}

    public function beforeDelete(Workspace $workspace): bool
    {
        return true;
    }

    public function afterDelete(Workspace $workspace): void {}
}
