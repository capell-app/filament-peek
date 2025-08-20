<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Components\Forms\Widget;

use Capell\Admin\Filament\Components\Forms\NameInput;
use Capell\Admin\Services\SlugGenerator;
use Capell\Core\Facades\CapellCore;
use Capell\Layout\Enums\LayoutModelEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class CreateWidgetDetailsSchema
{
    public static function make(Schema $schema): Grid
    {
        return Grid::make()
            ->visibleOn(['create', 'createOption', 'replicate'])
            ->schema(self::getSchema($schema))
            ->columnSpanFull();
    }

    private static function getSchema(Schema $schema): array
    {
        return [
            WidgetTypeSelect::make('type_id')
                ->live()
                ->columnSpanFull()
                ->withRelation()
                ->withCreateForm(),

            NameInput::make('name')
                ->afterStateUpdatedJs(
                    fn (NameInput $component): string => SlugGenerator::slugifyState("\$state ?? ''", 'key')
                ),

            TextInput::make('key')
                ->label(__('capell-admin::form.key'))
                ->placeholder(__('capell-admin::generic.key_placeholder'))
                ->alphaDash()
                ->required()
                ->maxLength(128)
                ->unique(
                    table: CapellCore::getModel(LayoutModelEnum::Widget->name),
                    ignoreRecord: $schema->getOperation() !== 'replicate',
                    modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed()
                ),
        ];
    }
}
