<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\FormMappings\Pages;

use Capell\Newsletter\Filament\Resources\FormMappings\FormMappingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFormMappings extends ListRecords
{
    protected static string $resource = FormMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
