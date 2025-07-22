<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Components\Forms\Widget;

use Capell\Admin\Enums\ContentEditorEnum;
use Capell\Admin\Filament\Components\Forms\ContentEditor;
use Capell\Admin\Filament\Components\Forms\RepeaterTabs;
use Capell\Admin\Filament\Components\Forms\TranslationLanguageSelect;
use Capell\Admin\Filament\Components\Forms\TranslationsRepeater;
use Capell\Core\Models\Translation;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class WidgetTranslationsRepeater
{
    public static function make(Schema $schema, array $components = []): RepeaterTabs
    {
        $contentEditor = $schema->getRecord()?->type->admin['content_editor'] ?? null;

        return TranslationsRepeater::make('translations')
            ->when(
                $schema->getOperation() === 'replicate',
                fn (TranslationsRepeater $repeater): TranslationsRepeater => $repeater->withoutRelationship()
            )
            ->schema([
                Hidden::make('is_title_changed_manually')
                    ->default(false)
                    ->dehydrated(false),

                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('capell-admin::form.title'))
                            ->afterStateUpdated(
                                fn (Set $set, $state): mixed => $set('is_title_changed_manually', (bool) $state)
                            )
                            ->columnSpan(fn (?Translation $record): int => $record instanceof Translation && $record->exists ? 3 : 2),

                        TranslationLanguageSelect::make()
                            ->hidden(fn (?Translation $record): bool => $record instanceof Translation && $record->exists),
                    ]),

                ContentEditor::make(
                    editor: $contentEditor ? ContentEditorEnum::tryFrom($contentEditor) : null
                ),

                ...$components,
            ]);
    }
}
