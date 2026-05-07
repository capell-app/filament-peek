<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\FormMappings\Pages;

use Capell\Newsletter\Filament\Resources\FormMappings\FormMappingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFormMapping extends EditRecord
{
    protected static string $resource = FormMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
