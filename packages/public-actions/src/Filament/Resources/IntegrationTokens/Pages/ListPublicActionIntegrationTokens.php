<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\IntegrationTokens\Pages;

use Capell\PublicActions\Filament\Resources\IntegrationTokens\PublicActionIntegrationTokenResource;
use Filament\Resources\Pages\ListRecords;

final class ListPublicActionIntegrationTokens extends ListRecords
{
    protected static string $resource = PublicActionIntegrationTokenResource::class;
}
