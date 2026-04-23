<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Filament\Components\Forms\Theme;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Grid;

class ColorsSchema
{
    public static function make(): array
    {
        return [
            Grid::make(['@sm' => 3])
                ->gridContainer()
                ->columnSpanFull()
                ->schema([
                    ColorPicker::make('link_color')
                        ->label(__('capell-admin::form.link_color'))
                        ->autoFormat(),
                    ColorPicker::make('link_color_active')
                        ->label(__('capell-admin::form.link_color_active'))
                        ->autoFormat(),
                    ColorPicker::make('divider_color')
                        ->label(__('capell-admin::form.divider_color'))
                        ->autoFormat(),
                ]),
            Checkbox::make('dark_mode_toggle')
                ->label(__('capell-admin::form.dark_mode_toggle'))
                ->columnSpanFull(),
            ColorsRepeater::make(),
        ];
    }
}
