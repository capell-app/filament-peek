<?php

declare(strict_types=1);

namespace Capell\Workspaces\Support;

use Capell\Core\Support\Subscriber\SubscriberManager;
use Capell\Workspaces\Events\Contracts\WorkspaceEventSubscriber;
use Illuminate\Support\Traits\Macroable;

/**
 * Centralized API for extending workspace functionality.
 *
 * Wraps the core SubscriberManager so the workspace-event vocabulary stays
 * domain-named while sharing the underlying storage + dispatch implementation.
 *
 * @extends SubscriberManager<WorkspaceEventSubscriber>
 */
class WorkspacesManager extends SubscriberManager
{
    use Macroable;
}
