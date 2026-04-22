<?php

declare(strict_types=1);

namespace Capell\Mosaic\Filament\Resources\Widgets\Schemas\Types\Assets;

use Capell\Admin\Facades\CapellAdmin;
use Capell\Mosaic\Enums\TypeSchemaEnum;
use Capell\Mosaic\Filament\Resources\Sections\Schemas\Types\DefaultSectionSchema;
use Filament\Schemas\Schema;
use Override;

class SectionWidgetAssetForm extends AbstractWidgetAssetSchema
{
    #[Override]
    protected function getAssetSchema(Schema $schema): array
    {
        $adminSchema = CapellAdmin::getSchema(TypeSchemaEnum::Section, DefaultSectionSchema::getKey());

        return resolve($adminSchema)->make($schema);
    }
}
