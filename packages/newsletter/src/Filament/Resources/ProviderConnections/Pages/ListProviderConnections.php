<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderConnections\Pages;

use Capell\Newsletter\Filament\Resources\ProviderConnections\ProviderConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProviderConnections extends ListRecords
{
    protected static string $resource = ProviderConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
