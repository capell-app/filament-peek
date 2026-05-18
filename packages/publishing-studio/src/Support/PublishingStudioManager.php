<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Support;

use Capell\Core\Support\Subscriber\SubscriberManager;
use Capell\PublishingStudio\Events\Contracts\WorkspaceEventSubscriber;
use Illuminate\Support\Traits\Macroable;
use Override;

/**
 * Centralized API for extending workspace functionality.
 * Similar to CapellAdminManager in the admin package.
 *
 * @extends SubscriberManager<WorkspaceEventSubscriber>
 */
class PublishingStudioManager extends SubscriberManager
{
    use Macroable;

    #[Override]
    protected function subscriberContract(): string
    {
        return WorkspaceEventSubscriber::class;
    }
}
