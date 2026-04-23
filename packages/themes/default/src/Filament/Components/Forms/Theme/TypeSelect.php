<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Filament\Components\Forms\Theme;

use Capell\Admin\Filament\Components\Forms\TypeSelect as BaseTypeSelect;
use Capell\Core\Enums\TypeEnum;

class TypeSelect extends BaseTypeSelect
{
    protected null|TypeEnum|string $type = TypeEnum::Theme;
}
