<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Components\Forms\Content;

use Capell\Admin\Filament\Components\Forms\TypeSelect;
use Capell\Core\Enums\TypeEnum;

class ContentTypeSelect extends TypeSelect
{
    protected ?TypeEnum $type = TypeEnum::Content;
}
