<?php

declare(strict_types=1);

namespace Capell\Layout\Enums;

use Capell\Admin\Filament\Schemas;

enum LayoutContainerSchemaEnum: string
{
    case Default = Schemas\Layout\DefaultLayoutContainerSchema::class;
}
