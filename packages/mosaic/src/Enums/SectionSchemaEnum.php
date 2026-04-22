<?php

declare(strict_types=1);

namespace Capell\Mosaic\Enums;

use Capell\Mosaic\Filament\Resources\Sections\Schemas\Types\DefaultSectionSchema;
use Capell\Mosaic\Filament\Resources\Sections\Schemas\Types\HeroSectionSchema;
use Capell\Mosaic\Filament\Resources\Sections\Schemas\Types\TestimonialSectionSchema;

enum SectionSchemaEnum: string
{
    case Default = DefaultSectionSchema::class;

    case Hero = HeroSectionSchema::class;

    case Testimonial = TestimonialSectionSchema::class;
}
