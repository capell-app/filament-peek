<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderInterestMappings\Pages;

use Capell\Newsletter\Filament\Resources\ProviderInterestMappings\ProviderInterestMappingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListProviderInterestMappings extends ListRecords
{
    protected static string $resource = ProviderInterestMappingResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
