<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Components\Forms\Widget;

use Capell\Admin\Facades\CapellAdmin;
use Capell\Core\Enums\ComponentEnum;
use Capell\Core\Models;
use Filament\Forms;
use Filament\Forms\Get;

class WidgetComponentFilesSection
{
    public static function make(bool $componentRequired = false): Forms\Components\Section
    {
        return Forms\Components\Section::make(function (Get $get, null|Models\Widget|Models\Type $record): string {
            if ($record === null) {
                return '';
            }

            $name = $record instanceof Models\Widget ? $record->type->name : $record->name;

            return __('capell-admin::generic.widget_files_description', ['name' => $name]);
        })
            ->icon('heroicon-o-paper-clip')
            ->collapsed()
            ->compact()
            ->columns(['lg' => 2, '2xl' => 3])
            ->columnSpanFull()
            ->schema([
                Forms\Components\Select::make('component')
                    ->label(__('capell-admin::form.component'))
                    ->searchable()
                    ->reactive()
                    ->preload()
                    ->when($componentRequired, fn (Forms\Components\Select $component): Forms\Components\Select => $component->required())
                    ->options(function (null|Models\Widget|Models\Type $record, Get $get): array {
                        if ($record === null) {
                            return [];
                        }

                        return CapellAdmin::getComponents(ComponentEnum::Widget);
                    }),

                Forms\Components\TextInput::make('file_view')
                    ->label(__('capell-admin::form.component_file_view'))
                    ->helperText(__('capell-admin::generic.component_file_view_info')),

                Forms\Components\Select::make('component_item')
                    ->label(__('capell-admin::form.component_item'))
                    ->helperText(__('capell-admin::generic.component_item_info'))
                    ->options(
                        fn (null|Models\Widget|Models\Type $record): array => CapellAdmin::getComponents(ComponentEnum::Resource)
                    ),
            ]);
    }
}
