<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderInterestMappings\Pages;

use Capell\Newsletter\Filament\Resources\ProviderInterestMappings\ProviderInterestMappingResource;
use Filament\Resources\Pages\EditRecord;

class EditProviderInterestMapping extends EditRecord
{
    protected static string $resource = ProviderInterestMappingResource::class;
}
