<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Filament\Components\Forms\Theme;

use Capell\Admin\Filament\Components\Forms\KeyTextInput;
use Capell\Admin\Filament\Components\Forms\NameKeyGroup;
use Capell\Core\Enums\ModelEnum;
use Capell\Core\Facades\CapellCore;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class DetailsSchema
{
    public static function make(Schema $schema): array
    {
        return [
            NameKeyGroup::make(
                modifyKey: fn (KeyTextInput $component): KeyTextInput => $component->unique(
                    table: CapellCore::getModel(ModelEnum::Theme),
                    ignoreRecord: $schema->getOperation() !== 'replicate',
                    modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed(),
                ),
            ),
        ];
    }
}
