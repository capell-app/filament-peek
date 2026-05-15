<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Filament\Resources\PreviewLinks\Pages;

use Capell\Admin\Support\AdminSurfaceLookup;
use Capell\PublishingStudio\Enums\ResourceEnum;
use Filament\Resources\Pages\ManageRecords;

class ManagePreviewLinks extends ManageRecords
{
    public static function getResource(): string
    {
        return AdminSurfaceLookup::resource(ResourceEnum::PreviewLink);
    }

    protected function getActions(): array
    {
        return [];
    }
}
