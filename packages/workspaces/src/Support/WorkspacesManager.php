<?php

declare(strict_types=1);

namespace Capell\Workspaces\Support;

use Capell\Workspaces\Events\Contracts\WorkspaceEventSubscriber;
use Illuminate\Support\Traits\Macroable;

/**
 * Centralized API for extending workspace functionality.
 * Similar to CapellAdminManager in the admin package.
 */
class WorkspacesManager
{
    use Macroable;

    /** @var array<class-string<WorkspaceEventSubscriber>> */
    private array $subscribers = [];

    /**
     * Register a workspace event subscriber.
     *
     * @param  class-string<WorkspaceEventSubscriber>  $subscriberClass
     */
    public function subscribe(string $subscriberClass): void
    {
        $this->subscribers[$subscriberClass] = $subscriberClass;
    }

    /**
     * @return array<class-string<WorkspaceEventSubscriber>>
     */
    public function getSubscribers(): array
    {
        return array_values($this->subscribers);
    }

    /**
     * Check if a subscriber is registered.
     *
     * @param  class-string<WorkspaceEventSubscriber>  $subscriberClass
     */
    public function hasSubscriber(string $subscriberClass): bool
    {
        return isset($this->subscribers[$subscriberClass]);
    }
}
