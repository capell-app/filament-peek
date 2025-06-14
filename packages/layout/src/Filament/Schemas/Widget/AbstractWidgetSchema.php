<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Schemas\Widget;

use Capell\Admin\Enums\SchemaEnum;
use Capell\Admin\Filament\Schemas\AbstractSchema;

abstract class AbstractWidgetSchema extends AbstractSchema
{
    protected static SchemaEnum $schemaType = SchemaEnum::Widget;
}
