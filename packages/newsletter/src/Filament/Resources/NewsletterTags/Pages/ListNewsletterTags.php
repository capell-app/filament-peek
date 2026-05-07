<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\NewsletterTags\Pages;

use Capell\Newsletter\Filament\Resources\NewsletterTags\NewsletterTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNewsletterTags extends ListRecords
{
    protected static string $resource = NewsletterTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
