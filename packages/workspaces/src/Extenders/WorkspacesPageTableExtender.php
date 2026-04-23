<?php

declare(strict_types=1);

namespace Capell\Workspaces\Extenders;

use Capell\Admin\Contracts\Extenders\PageTableExtender;
use Capell\Workspaces\WorkspaceContextScope;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class WorkspacesPageTableExtender implements PageTableExtender
{
    /** @return array<int, Column> */
    public function getColumns(): array
    {
        return [];
    }

    /** @return array<int, BulkAction> */
    public function getBulkActions(): array
    {
        return [];
    }

    /** @return array<int, BaseFilter> */
    public function getFilters(): array
    {
        return [];
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->withoutGlobalScope(WorkspaceContextScope::class);
    }
}
