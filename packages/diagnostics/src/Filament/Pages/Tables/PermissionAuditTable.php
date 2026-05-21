<?php

declare(strict_types=1);

namespace Capell\Diagnostics\Filament\Pages\Tables;

use Capell\Admin\Filament\Contracts\TableConfigurator;
use Capell\Diagnostics\Actions\DashboardReports\BuildPermissionAuditQueryAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PermissionAuditTable implements TableConfigurator
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => BuildPermissionAuditQueryAction::run())
            ->columns([
                TextColumn::make('name')
                    ->label(__('capell-diagnostics::package.role'))
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label(__('capell-diagnostics::package.users'))
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label(__('capell-diagnostics::package.permissions'))
                    ->sortable(),
            ])
            ->striped()
            ->paginated();
    }
}
