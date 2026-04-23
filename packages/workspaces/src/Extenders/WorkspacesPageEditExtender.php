<?php

declare(strict_types=1);

namespace Capell\Workspaces\Extenders;

use Capell\Admin\Contracts\Extenders\PageEditExtender;

class WorkspacesPageEditExtender implements PageEditExtender
{
    /** @return array<int, mixed> */
    public function getFormActions(): array
    {
        return [];
    }

    /** @return array<int, mixed> */
    public function getHeaderWidgets(): array
    {
        return [];
    }
}
