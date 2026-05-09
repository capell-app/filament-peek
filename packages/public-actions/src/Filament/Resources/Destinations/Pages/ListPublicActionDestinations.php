<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\Destinations\Pages;

use Capell\PublicActions\Filament\Resources\Destinations\PublicActionDestinationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListPublicActionDestinations extends ListRecords
{
    protected static string $resource = PublicActionDestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
