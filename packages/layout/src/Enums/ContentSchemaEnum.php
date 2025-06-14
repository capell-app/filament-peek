<?php

declare(strict_types=1);

namespace Capell\Layout\Enums;

use Capell\Admin\Filament\Schemas;

enum ContentSchemaEnum: string
{
    case Default = Schemas\Content\DefaultContentSchema::class;
}
