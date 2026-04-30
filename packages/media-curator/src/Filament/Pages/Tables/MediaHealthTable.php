<?php

declare(strict_types=1);

namespace Capell\MediaCurator\Filament\Pages\Tables;

use Capell\Admin\Filament\Components\Tables\Columns\DateColumn;
use Capell\Admin\Filament\Contracts\TableConfigurator;
use Capell\Core\Models\Media;
use Capell\MediaCurator\Actions\Reports\BuildMediaHealthQueryAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MediaHealthTable implements TableConfigurator
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => BuildMediaHealthQueryAction::run())
            ->columns([
                TextColumn::make('name')
                    ->label(__('capell-admin::table.filename'))
                    ->size('sm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('size')
                    ->label(__('capell-admin::table.size'))
                    ->size('sm')
                    ->formatStateUsing(fn (?int $state): string => $state !== null ? round($state / 1024) . ' KB' : 'N/A')
                    ->sortable(),
                TextColumn::make('usage_count')
                    ->label(__('capell-admin::table.usage_count'))
                    ->size('sm')
                    ->state(fn (Media $record): int => $record->model_id !== null ? 1 : 0)
                    ->sortable(),
                TextColumn::make('media_type')
                    ->label(__('capell-admin::table.media_type'))
                    ->size('sm')
                    ->badge()
                    ->sortable(),
                DateColumn::make('updated_at')
                    ->label(__('capell-admin::table.last_used'))
                    ->size('sm')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'asc');
    }
}
