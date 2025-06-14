<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Components\Forms\Widget;

use Capell\Admin\Filament\Components\Forms\ContentEditorSection;
use Capell\Admin\Filament\Components\Forms\RepeaterTabs;
use Capell\Admin\Filament\Components\Forms\TranslationLanguageSelect;
use Capell\Admin\Filament\Components\Forms\TranslationsRepeater;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;

class WidgetTranslationsRepeater
{
    public static function make(string $operation, array $schema = []): RepeaterTabs
    {
        return TranslationsRepeater::make('translations')
            ->when(
                $operation === 'replicate',
                fn (TranslationsRepeater $repeater): TranslationsRepeater => $repeater->withoutRelationship()
            )
            ->schema([
                Forms\Components\Hidden::make('is_title_changed_manually')
                    ->default(false)
                    ->dehydrated(false),

                Forms\Components\Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('capell-admin::form.title'))
                            ->columnSpan(fn (Get $get): int => $get('language_id') ? 3 : 2)
                            ->afterStateUpdated(
                                fn (Set $set, $state): mixed => $set('is_title_changed_manually', (bool) $state)
                            ),

                        TranslationLanguageSelect::make(),
                    ]),

                ContentEditorSection::make(),

                ...$schema,
            ]);
    }
}
