<?php

declare(strict_types=1);

namespace Capell\Diagnostics\Filament\Pages\Tables;

use Capell\Admin\Filament\Contracts\TableConfigurator;
use Capell\Diagnostics\Actions\DashboardReports\BuildQueueHealthQueryAction;
use Capell\Diagnostics\Actions\DashboardReports\SummarizeFailedJobExceptionAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QueueHealthTable implements TableConfigurator
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => BuildQueueHealthQueryAction::run())
            ->columns([
                TextColumn::make('payload')
                    ->label(__('capell-diagnostics::package.job'))
                    ->formatStateUsing(function (string $payload): string {
                        $decoded = json_decode($payload, true);

                        return $decoded['displayName'] ?? 'Unknown Job';
                    }),
                TextColumn::make('queue')
                    ->label(__('capell-diagnostics::package.queue'))
                    ->sortable(),
                TextColumn::make('exception')
                    ->label(__('capell-diagnostics::package.exception'))
                    ->formatStateUsing(fn (?string $state): string => SummarizeFailedJobExceptionAction::run($state))
                    ->limit(100),
                TextColumn::make('failed_at')
                    ->label(__('capell-diagnostics::package.failed_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->striped()
            ->paginated();
    }
}
