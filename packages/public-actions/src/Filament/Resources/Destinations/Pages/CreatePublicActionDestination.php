<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\Destinations\Pages;

use Capell\PublicActions\Filament\Resources\Destinations\PublicActionDestinationResource;
use Filament\Resources\Pages\CreateRecord;

final class CreatePublicActionDestination extends CreateRecord
{
    protected static string $resource = PublicActionDestinationResource::class;
}
