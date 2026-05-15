<?php

declare(strict_types=1);

namespace Capell\Events\Filament\Resources\Venues\Pages;

use Capell\Admin\Support\AdminSurfaceLookup;
use Capell\Events\Enums\ResourceEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Override;

class ManageEventVenues extends ManageRecords
{
    #[Override]
    public static function getResource(): string
    {
        return AdminSurfaceLookup::resource(ResourceEnum::EventVenue);
    }

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
