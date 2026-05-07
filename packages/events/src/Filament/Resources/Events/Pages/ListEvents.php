<?php

declare(strict_types=1);

namespace Capell\Events\Filament\Resources\Events\Pages;

use Capell\Admin\Enums\ResourceEnum as AdminResourceEnum;
use Capell\Admin\Filament\Resources\Pages\Pages\ListPages;
use Capell\Admin\Support\AdminSurfaceLookup;
use Capell\Events\Enums\ResourceEnum;
use Capell\Events\Filament\Resources\Events\EventResource;
use Illuminate\Contracts\Support\Htmlable;

class ListEvents extends ListPages
{
    /** @return class-string<EventResource> */
    public static function getResource(): string
    {
        return AdminSurfaceLookup::resourceIfRegistered(AdminResourceEnum::Page, strtolower(ResourceEnum::Event->name))
            ?? EventResource::class;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('capell-events::generic.events_info');
    }
}
