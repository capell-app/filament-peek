<?php

declare(strict_types=1);

namespace Capell\Workspaces\Contracts;

interface WorkspaceTableActionContributor
{
    public const TAG = 'capell.workspaces.table_action_contributors';

    /**
     * @return array<int, object>
     */
    public function actions(): array;
}
