<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Filament\Settings;

use Capell\Admin\Filament\Contracts\HasSchema;
use Capell\SeoSuite\Filament\Components\Forms\Site\MetaSchema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class StructuredDataSettingsSchema implements HasSchema
{
    public static function make(Schema $configurator): array
    {
        return [
            Grid::make()
                ->columnSpanFull()
                ->schema([
                    MetaSchema::make(),
                ]),
        ];
    }
}
