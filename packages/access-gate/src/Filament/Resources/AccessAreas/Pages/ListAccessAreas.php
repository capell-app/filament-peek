<?php

declare(strict_types=1);

namespace Capell\AccessGate\Filament\Resources\AccessAreas\Pages;

use Capell\AccessGate\Filament\Resources\AccessAreas\AccessAreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListAccessAreas extends ListRecords
{
    protected static string $resource = AccessAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
