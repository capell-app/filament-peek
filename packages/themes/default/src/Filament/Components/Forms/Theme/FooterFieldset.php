<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Filament\Components\Forms\Theme;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Override;

class FooterFieldset extends Fieldset
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('capell-admin::form.footer'))
            ->schema([
                Checkbox::make('footer')
                    ->label(__('capell-admin::form.has_footer'))
                    ->default(true),
                Grid::make()
                    ->columnSpanFull()
                    ->visibleJs(<<<'JS'
                         $get('footer')
                    JS)
                    ->schema([
                        Grid::make(['@sm' => 3])
                            ->gridContainer()
                            ->columnSpanFull()
                            ->schema($this->getFooterColorSchema()),
                        Section::make(__('capell-admin::form.dark_color'))
                            ->compact()
                            ->collapsed()
                            ->columnSpanFull()
                            ->schema([
                                Grid::make(['@sm' => 3])
                                    ->gridContainer()
                                    ->schema(
                                        $this->getFooterColorSchema(
                                            color: 'footer_dark_color',
                                            backgroundColor: 'footer_dark_background_color',
                                        ),
                                    ),
                            ]),
                        TextInput::make('footer_file')
                            ->label(__('capell-admin::form.file'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private function getFooterColorSchema(
        string $color = 'footer_color',
        string $backgroundColor = 'footer_background_color',
        string $borderColor = 'footer_border_color',
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
