<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Support;

use Capell\Core\Models\Page;
use Capell\PublishingStudio\Models\Version;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\Models\WorkspaceReviewAssignment;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class WorkspaceSchema
{
    public static function isReady(): bool
    {
        try {
            return self::hasWorkspaceTable()
                && Schema::hasTable((new Version)->getTable())
                && Schema::hasTable((new WorkspaceReviewAssignment)->getTable())
                && Schema::hasColumn((new Page)->getTable(), 'workspace_id');
        } catch (Throwable) {
            return false;
        }
    }

    public static function hasWorkspaceTable(): bool
    {
        try {
            return Schema::hasTable((new Workspace)->getTable());
        } catch (Throwable) {
            return false;
        }
    }
}
