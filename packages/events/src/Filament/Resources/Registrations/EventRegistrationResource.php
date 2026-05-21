<?php

declare(strict_types=1);

namespace Capell\Events\Filament\Resources\Registrations;

use BackedEnum;
use Capell\Admin\Support\SiteScope;
use Capell\Events\Filament\Resources\Registrations\Pages\ManageEventRegistrations;
use Capell\Events\Models\EventRegistration;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

class EventRegistrationResource extends Resource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    #[Override]
    public static function getModel(): string
    {
        return EventRegistration::class;
    }

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('capell-events::generic.event_registrations');
    }

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return (string) __('capell-admin::navigation.group_content');
    }

    #[Override]
    public static function getNavigationParentItem(): ?string
    {
        return (string) __('capell-events::generic.events');
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('capell-events::table.name'))->searchable(),
            TextColumn::make('email')->label(__('capell-events::table.email'))->searchable(),
            TextColumn::make('occurrence.event.name')->label(__('capell-events::table.event')),
            TextColumn::make('status')->label(__('capell-events::table.status'))->badge(),
            TextColumn::make('quantity')->label(__('capell-events::table.quantity')),
        ]);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ManageEventRegistrations::route('/'),
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('occurrence.event', fn (Builder $query): Builder => SiteScope::applyForCurrentActor($query));
    }
}
