<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderAudiences\Pages;

use Capell\Newsletter\Filament\Resources\ProviderAudiences\ProviderAudienceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListProviderAudiences extends ListRecords
{
    protected static string $resource = ProviderAudienceResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
