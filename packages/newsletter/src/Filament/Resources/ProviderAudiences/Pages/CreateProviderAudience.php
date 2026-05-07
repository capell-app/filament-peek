<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderAudiences\Pages;

use Capell\Newsletter\Filament\Resources\ProviderAudiences\ProviderAudienceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProviderAudience extends CreateRecord
{
    protected static string $resource = ProviderAudienceResource::class;
}
