<?php

declare(strict_types=1);

namespace Capell\Mosaic\Enums;

use Capell\Mosaic\Filament\Resources\Widgets\Schemas\Types\HeroWidgetSchema;

enum WidgetSchemaEnum: string
{
    case Hero = HeroWidgetSchema::class;
}
