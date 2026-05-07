<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderConnections\Pages;

use Capell\Newsletter\Filament\Resources\ProviderConnections\ProviderConnectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProviderConnection extends CreateRecord
{
    protected static string $resource = ProviderConnectionResource::class;
}
