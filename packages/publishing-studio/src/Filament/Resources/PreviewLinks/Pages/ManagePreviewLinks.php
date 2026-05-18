<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Filament\Resources\PreviewLinks\Pages;

use Capell\Admin\Support\AdminSurfaceLookup;
use Capell\PublishingStudio\Enums\ResourceEnum;
use Filament\Resources\Pages\ManageRecords;
use Override;

class ManagePreviewLinks extends ManageRecords
{
    #[Override]
    public static function getResource(): string
    {
        return AdminSurfaceLookup::resource(ResourceEnum::PreviewLink);
    }

    #[Override]
    protected function getActions(): array
    {
        return [];
    }
}
