<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Filament\Resources\Documents\RelationManagers;

use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Override;

final class AcceptancesRelationManager extends RelationManager
{
    protected static string|BackedEnum|null $icon = 'heroicon-o-check-badge';

    protected static string $relationship = 'acceptances';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('capell-document-lifecycle::navigation.relations.acceptances');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->latest('accepted_at')->latest('id'))
            ->columns([
                TextColumn::make('document_version')
                    ->label(__('capell-document-lifecycle::navigation.fields.version'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('document_hash')
                    ->label(__('capell-document-lifecycle::navigation.fields.hash'))
                    ->copyable()
                    ->limit(16)
                    ->searchable(),
                TextColumn::make('context')
                    ->label(__('capell-document-lifecycle::navigation.fields.context'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('acceptor_type')
                    ->label(__('capell-document-lifecycle::navigation.fields.acceptor'))
                    ->formatStateUsing(fn (?string $state): string => class_basename($state ?? 'Unknown')),
                TextColumn::make('accepted_at')
                    ->label(__('capell-document-lifecycle::navigation.fields.accepted_at'))
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    #[Override]
    protected static function getPluralModelLabel(): string
    {
        return __('capell-document-lifecycle::navigation.relations.acceptances');
    }
}
