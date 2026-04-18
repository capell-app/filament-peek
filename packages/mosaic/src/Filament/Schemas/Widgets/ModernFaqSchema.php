<?php

declare(strict_types=1);

namespace Capell\Mosaic\Filament\Schemas\Widgets;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

/**
 * Filament Schema for Modern FAQ Section Widget
 *
 * Provides admin panel controls for customizing FAQ accordion
 * content and display options.
 */
class ModernFaqSchema
{
    public static function getFormSchema(): array
    {
        return [
            Section::make('Content')
                ->description('FAQ section title')
                ->schema([
                    TextInput::make('data.title')
                        ->label('Section Title')
                        ->placeholder('Frequently Asked Questions')
                        ->columnSpanFull(),
                ])->columns(1),

            Section::make('Display')
                ->description('Visibility and admin hints')
                ->schema([
                    Toggle::make('data.customizable')
                        ->label('Show Admin Hints')
                        ->default(true)
                        ->helperText('Display "✨ Customize..." message'),
                ])->columns(1),
        ];
    }

    public static function getDefaults(): array
    {
        return [
            'title' => 'Frequently Asked Questions',
            'customizable' => true,
        ];
    }
}
