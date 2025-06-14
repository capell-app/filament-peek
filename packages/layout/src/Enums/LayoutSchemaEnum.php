<?php

declare(strict_types=1);

namespace Capell\Layout\Enums;

use Capell\Admin\Filament\Schemas;

enum LayoutSchemaEnum: string
{
    case Default = Schemas\Layout\DefaultLayoutSchema::class;
}
