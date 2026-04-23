<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Filament\Components\Forms\Theme;

use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Override;

class MainFieldset extends Fieldset
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('capell-admin::form.main'))
            ->schema([
                Grid::make()
                    ->columnSpanFull()
                    ->schema([
                        ...$this->getMainColorSchema(),
                        Section::make(__('capell-admin::form.dark_color'))
                            ->compact()
                            ->collapsed()
                            ->columnSpanFull()
                            ->schema(
                                $this->getMainColorSchema(
                                    backgroundColor: 'main_dark_background_color',
                                ),
                            ),
                    ]),
            ]);
    }

    private function getMainColorSchema(
        string $backgroundColor = 'main_background_color',
    ): array {
        return [
            ColorPicker::make($backgroundColor)
                ->label(__('capell-admin::form.background_color'))
                ->autoFormat(),
        ];
    }
}
