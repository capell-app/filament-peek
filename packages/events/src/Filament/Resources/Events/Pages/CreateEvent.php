<?php

declare(strict_types=1);

namespace Capell\Events\Filament\Resources\Events\Pages;

use Capell\Admin\Enums\ResourceEnum as AdminResourceEnum;
use Capell\Admin\Filament\Resources\Pages\Pages\CreatePage;
use Capell\Admin\Support\AdminSurfaceLookup;
use Capell\Core\Models\Type;
use Capell\Events\Actions\EnsureEventPublishingDefaultsAction;
use Capell\Events\Enums\ResourceEnum;
use Capell\Events\Filament\Resources\Events\EventResource;

class CreateEvent extends CreatePage
{
    /** @return class-string<EventResource> */
    public static function getResource(): string
    {
        return AdminSurfaceLookup::resourceIfRegistered(AdminResourceEnum::Page, strtolower(ResourceEnum::Event->name))
            ?? EventResource::class;
    }

    protected function beforeFill(): void
    {
        parent::beforeFill();

        $defaults = resolve(EnsureEventPublishingDefaultsAction::class);

        $this->data['layout_id'] = $defaults->defaultEventLayout()->getKey();

        /** @var class-string<Type> $model */
        $model = Type::class;

        $this->data['type_id'] = $model::query()
            ->pageType()
            ->where('key', 'event')
            ->value('id') ?? $defaults->eventPageType()->getKey();
    }
}
