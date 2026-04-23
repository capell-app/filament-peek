<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Filament\Components\Forms\Theme;

use Capell\Core\Enums\HeaderPositionEnum;
use Capell\Core\Enums\MenuAlignmentEnum;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Override;

class HeaderFieldset extends Fieldset
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('capell-admin::form.header'))
            ->schema([
                Checkbox::make('header')
                    ->label(__('capell-admin::form.has_header'))
                    ->default(true),
                Grid::make()
                    ->columnSpanFull()
                    ->visibleJs(<<<'JS'
                         $get('header')
                    JS)
                    ->schema([
                        Grid::make(['@sm' => 3])
                            ->gridContainer()
                            ->columnSpanFull()
                            ->schema($this->getHeaderColorSchema()),
                        Section::make(__('capell-admin::form.dark_color'))
                            ->compact()
                            ->collapsed()
                            ->columnSpanFull()
                            ->schema([
                                Grid::make(['@sm' => 3])
                                    ->gridContainer()
                                    ->schema(
                                        $this->getHeaderColorSchema(
                                            color: 'header_dark_color',
                                            backgroundColor: 'header_dark_background_color',
                                        ),
                                    ),
                            ]),
                        TextInput::make('header_file')
                            ->label(__('capell-admin::form.file'))
                            ->columnSpanFull(),
                        Grid::make(['@sm' => 3])
                            ->gridContainer()
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('header_height')
                                    ->label(__('capell-admin::form.height'))
                                    ->placeholder('4rem'),
                                Select::make('header_position')
                                    ->label(__('capell-admin::form.header_position'))
                                    ->options(HeaderPositionEnum::class)
                                    ->default(HeaderPositionEnum::Static_),
                                Select::make('header_menu_alignment')
                                    ->label(__('capell-admin::form.header_menu_alignment'))
                                    ->options(MenuAlignmentEnum::class),
                            ]),
                    ]),
            ]);
    }

    private function getHeaderColorSchema(
        string $color = 'header_color',
        string $backgroundColor = 'header_background_color',
        string $borderColor = 'header_border_color',
    ): array {
        return [
            ColorPicker::make($backgroundColor)
                ->label(__('capell-admin::form.background_color'))
                ->autoFormat(),
            ColorPicker::make($borderColor)
                ->label(__('capell-admin::form.border_color'))
                ->autoFormat(),
            ColorPicker::make($color)
                ->label(__('capell-admin::form.text_color'))
                ->autoFormat(),
        ];
    }
}
