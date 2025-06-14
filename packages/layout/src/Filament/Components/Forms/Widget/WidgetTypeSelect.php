<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Components\Forms\Widget;

use Capell\Admin\Filament\Components\Forms\TypeSelect;
use Capell\Core\Enums\TypeEnum;

class WidgetTypeSelect extends TypeSelect
{
    protected ?TypeEnum $type = TypeEnum::Widget;
}
