<?php

declare(strict_types=1);

namespace Capell\Events\Filament\Resources\Events\Pages;

use Capell\Admin\Enums\ResourceEnum as AdminResourceEnum;
use Capell\Admin\Filament\Resources\Pages\Pages\EditPage;
use Capell\Admin\Support\AdminSurfaceLookup;
use Capell\Events\Enums\ResourceEnum;
use Capell\Events\Filament\Resources\Events\EventResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class EditEvent extends EditPage
{
    use InteractsWithRecord { resolveRecord as baseResolveRecord; }

    public static function getResource(): string
    {
        return AdminSurfaceLookup::resourceIfRegistered(AdminResourceEnum::Page, strtolower(ResourceEnum::Event->name))
            ?? EventResource::class;
    }
}
