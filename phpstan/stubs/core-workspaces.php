<?php

declare(strict_types=1);

namespace Capell\Workspaces;

trait BelongsToWorkspace
{
    public function isLive(): bool {}
}
