<?php

declare(strict_types=1);

namespace Capell\Mosaic\Enums;

use Capell\Mosaic\Filament\Resources\Widgets\Schemas\Types\Assets\PageWidgetAssetForm;
use Capell\Mosaic\Filament\Resources\Widgets\Schemas\Types\Assets\SectionWidgetAssetForm;
use InvalidArgumentException;

enum WidgetAssetSchemaEnum: string
{
    case Section = SectionWidgetAssetForm::class;

    case Page = PageWidgetAssetForm::class;

    public static function fromName(string $name): self
    {
        throw_if($name === '' || $name === '0', InvalidArgumentException::class, 'WidgetAssetSchemaEnum name cannot be empty');

        return constant(self::class . ('::' . $name))
            ?? throw new InvalidArgumentException('Invalid WidgetAssetSchemaEnum name: ' . $name);
    }
}
