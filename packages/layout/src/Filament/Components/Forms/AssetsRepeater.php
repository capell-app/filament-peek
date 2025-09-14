<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Components\Forms;

use Capell\Core\Data\AssetData;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models\Page;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NestedSet;

class AssetsRepeater extends Repeater
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->relationship()
            ->defaultItems(1)
            ->table([
                TableColumn::make(__('capell-admin::form.type'))
                    ->width('10rem'),
                TableColumn::make(__('capell-admin::form.asset')),
            ])
            ->schema(self::getFormSchema());
    }

    protected static function getFormSchema(): array
    {
        return [
            AssetTypeToggleButtons::make('asset_type')
                ->required()
                ->maxWidth(Width::Full)
                ->afterStateUpdatedJs(fn ($state): string => <<<'JS'
                    if ($state !== $old) { $set('asset_id', null); }
                JS),
            Select::make('asset_id')
                ->label(__('capell-layout::form.select_add_asset_type'))
                ->required()
                ->searchable()
                ->getSearchResultsUsing(
                    static fn (Select $component, Get $get, string $search): array => self::getAssetOptions(
                        $component,
                        $get('asset_type'),
                        limit: $component->getOptionsLimit(),
                        search: $search
                    )
                )
                ->options(
                    fn (Select $component, Get $get): array => self::getAssetOptions(
                        $component,
                        $get('asset_type'),
                        limit: $component->getOptionsLimit()
                    )
                ),
        ];
    }

    protected static function getAssetOptionsFromResults($results, AssetData $asset): Collection
    {
        if ($asset->name === 'Page') {
            return self::getPageAssetOptions($results);
        }

        return $results->pluck('name', 'id');
    }

    protected static function getPageAssetOptions($results): Collection
    {
        $options = collect();

        $results->each(function (Page $page) use (&$options): void {
            $label = $page->site->name . ' » ';

            $ancestors = $page->ancestors()->get();

            if ($ancestors->isNotEmpty()) {
                $label .= $ancestors->pluck('name')
                    ->map(fn ($item) => Str::limit($item, 30))
                    ->implode(' » ')
                    . ' » ';
            }

            $label .= Str::limit($page->name, 40);

            $options->put($page->id, $label);
        });

        return $options;
    }

    private static function getAssetOptions(Select $component, ?string $type, int $limit = 10, ?string $search = null): array
    {
        if ($type === null || $type === '' || $type === '0') {
            return [];
        }

        $asset = CapellCore::getAsset($type);

        /* @var class-string<Model> $model */
        $model = $asset->model;

        $query = $model::query()
            ->select([
                'id',
                'id',
                'name',
            ])
            ->when(
                $asset->name === 'Page',
                fn (BuilderContract $query) => $query->with([
                    'ancestors' => fn (Relation $query) => $query->withDrafts(),
                    'site',
                ])
                    ->addSelect([
                        'pages.site_id',
                        'pages.parent_id',
                        'pages._lft',
                        'pages._rgt',
                    ])
                    ->withDrafts()
                    ->orderBy('site_id')
                    ->orderBy(NestedSet::LFT, 'DESC')
                    ->whereHas(
                        'type',
                        fn (Builder $query) => $query->where(
                            fn (Builder $query) => $query->where('group', '!=', 'system')
                                ->orWhereNull('group')
                        )
                    )
            )
            ->when(
                $search,
                fn (Builder $query, string $search): Builder => $query->where(
                    'name',
                    'like',
                    sprintf('%%%s%%', $search)
                )
            );

        $total = $query->count();

        $results = $query->limit($limit)->get();

        $options = self::getAssetOptionsFromResults($results, $asset);

        if ($total > $limit) {
            $options->pop();
            $options->put(null, __('capell-admin::form.more_results', ['count' => $total - $limit]));
            $component->disableOptionWhen(fn (string $value): bool => ! $value);
        }

        return $options->toArray();
    }
}
