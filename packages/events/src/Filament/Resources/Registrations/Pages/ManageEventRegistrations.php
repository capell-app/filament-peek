<?php

declare(strict_types=1);

namespace Capell\Events\Filament\Resources\Registrations\Pages;

use Capell\Admin\Support\AdminSurfaceLookup;
use Capell\Events\Enums\ResourceEnum;
use Filament\Resources\Pages\ManageRecords;
use Override;

class ManageEventRegistrations extends ManageRecords
{
    #[Override]
    public static function getResource(): string
    {
        return AdminSurfaceLookup::resource(ResourceEnum::EventRegistration);
    }
}
