<?php

declare(strict_types=1);

namespace Capell\Mosaic\Enums;

use Capell\Mosaic\Filament\Resources\Contents\Schemas\Types\HeroContentSchema;

enum ContentSchemaEnum: string
{
    case Hero = HeroContentSchema::class;
}
