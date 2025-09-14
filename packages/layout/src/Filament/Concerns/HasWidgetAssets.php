<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Concerns;

use Capell\Admin\Contracts\TypeSchemaInterface;
use Capell\Layout\Filament\Components\Forms\AssetsRepeater;
use Capell\Layout\Filament\Resources\Widgets\RelationManagers\WidgetAssetsRelationManager;
use Capell\Layout\Models\WidgetAsset;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin TypeSchemaInterface
 */
trait HasWidgetAssets
{
    /**
     * @param  WidgetAsset  $record
     */
    public static function relationManagers(Model $record): array
    {
        return [
            WidgetAssetsRelationManager::class,
            ...parent::relationManagers($record),
        ];
    }

    protected static function getAssetsComponent(Schema $schema): Component
    {
        return AssetsRepeater::make('assets')
            ->hiddenLabel()
            ->reorderable();
    }
}
