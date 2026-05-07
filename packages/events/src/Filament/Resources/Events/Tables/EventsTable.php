<?php

declare(strict_types=1);

namespace Capell\Events\Filament\Resources\Events\Tables;

use Capell\Admin\Filament\Components\Tables\Actions\EditAction;
use Capell\Admin\Filament\Components\Tables\Columns\DateColumn;
use Capell\Admin\Filament\Components\Tables\Columns\IdentifierColumn;
use Capell\Admin\Filament\Components\Tables\Columns\NameColumn;
use Capell\Admin\Filament\Contracts\TableConfigurator;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsTable implements TableConfigurator
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IdentifierColumn::make('id'),
                NameColumn::make('name')->defaultBadge(),
                TextColumn::make('starts_at')
                    ->label(__('capell-events::table.starts_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('venue.name')
                    ->label(__('capell-events::table.venue'))
                    ->toggleable(),
                TextColumn::make('visibility')
                    ->label(__('capell-events::table.visibility'))
                    ->badge(),
                DateColumn::make('created_at'),
                DateColumn::make('updated_at'),
            ])
            ->recordActions([
                EditAction::make('edit'),
                DeleteAction::make('delete'),
            ]);
    }
}
