<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\FormMappings\Pages;

use Capell\Newsletter\Filament\Resources\FormMappings\FormMappingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFormMapping extends CreateRecord
{
    protected static string $resource = FormMappingResource::class;
}
