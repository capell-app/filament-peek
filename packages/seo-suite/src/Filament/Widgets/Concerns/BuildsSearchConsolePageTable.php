<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Filament\Widgets\Concerns;

use Capell\SeoSuite\Actions\Dashboard\BuildSearchConsolePageRowsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

trait BuildsSearchConsolePageTable
{
    abstract protected function mode(): string;

    abstract protected function heading(): string;

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): Collection => BuildSearchConsolePageRowsAction::run($this->mode(), 5))
            ->queryStringIdentifier('seo-' . $this->mode() . '-search-pages')
            ->paginated(false)
            ->searchable(false)
            ->heading($this->heading())
            ->emptyStateHeading(__('capell-seo-suite::dashboard.no_search_console_rows'))
            ->emptyStateDescription(__('capell-seo-suite::dashboard.no_search_console_rows_description'))
            ->columns([
                TextColumn::make('url')
                    ->label(__('capell-seo-suite::dashboard.url'))
                    ->limit(60)
                    ->tooltip(fn (mixed $state): ?string => is_string($state) && $state !== '' ? $state : null)
                    ->wrap(),
                TextColumn::make('direction')
                    ->label(__('capell-seo-suite::dashboard.direction')),
                TextColumn::make('clicks')
                    ->label(__('capell-seo-suite::dashboard.clicks'))
                    ->numeric(),
                TextColumn::make('impressions')
                    ->label(__('capell-seo-suite::dashboard.impressions'))
                    ->numeric(),
                TextColumn::make('ctr')
                    ->label(__('capell-seo-suite::dashboard.ctr'))
                    ->formatStateUsing(fn (mixed $state): string => number_format((float) $state, 1) . '%'),
                TextColumn::make('average_position')
                    ->label(__('capell-seo-suite::dashboard.average_position')),
                TextColumn::make('click_delta')
                    ->label(__('capell-seo-suite::dashboard.click_delta'))
                    ->numeric(),
            ]);
    }
}
