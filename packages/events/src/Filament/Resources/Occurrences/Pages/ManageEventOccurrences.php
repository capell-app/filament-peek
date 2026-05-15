<?php

declare(strict_types=1);

namespace Capell\Events\Filament\Resources\Occurrences\Pages;

use Capell\Admin\Support\AdminSurfaceLookup;
use Capell\Events\Enums\ResourceEnum;
use Filament\Resources\Pages\ManageRecords;
use Override;

class ManageEventOccurrences extends ManageRecords
{
    #[Override]
    public static function getResource(): string
    {
        return AdminSurfaceLookup::resource(ResourceEnum::EventOccurrence);
    }
}
