<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Bridges\PublishingStudio;

use Capell\Core\Facades\CapellCore;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\WorkspaceContext;

/**
 * The only place Filament Peek touches Publishing Studio's workspaces. Without Publishing Studio
 * there is no workspace to attach a preview to, and previews render in the current context.
 */
final class PeekWorkspaceBridge
{
    public static function currentWorkspaceId(): ?int
    {
        if (! CapellCore::isPackageAvailable('capell-app/publishing-studio')) {
            return null;
        }

        $workspaceId = WorkspaceContext::currentId();

        return is_int($workspaceId) ? $workspaceId : null;
    }

    /**
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function runInWorkspace(?int $workspaceId, callable $callback): mixed
    {
        if (! CapellCore::isPackageAvailable('capell-app/publishing-studio')) {
            return $callback();
        }

        $workspace = $workspaceId === null ? null : Workspace::query()->find($workspaceId);

        return WorkspaceContext::runWith($workspace, $callback);
    }
}
