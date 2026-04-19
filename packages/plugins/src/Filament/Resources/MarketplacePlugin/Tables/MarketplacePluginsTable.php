<?php

declare(strict_types=1);

namespace Capell\Plugins\Filament\Resources\MarketplacePlugin\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MarketplacePluginsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vendor')
                    ->label(__('Vendor'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kind')
                    ->label(__('Kind'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('license_model')
                    ->label(__('License Model'))
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label(__('Visible'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
