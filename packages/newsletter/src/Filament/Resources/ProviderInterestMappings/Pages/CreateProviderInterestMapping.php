<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderInterestMappings\Pages;

use Capell\Newsletter\Filament\Resources\ProviderInterestMappings\ProviderInterestMappingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProviderInterestMapping extends CreateRecord
{
    protected static string $resource = ProviderInterestMappingResource::class;
}
