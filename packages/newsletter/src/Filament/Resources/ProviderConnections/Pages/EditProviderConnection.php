<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderConnections\Pages;

use Capell\Newsletter\Filament\Resources\ProviderConnections\ProviderConnectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProviderConnection extends EditRecord
{
    protected static string $resource = ProviderConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
