<?php

declare(strict_types=1);

namespace Capell\AccessGate\Filament\Resources\Grants\Pages;

use Capell\AccessGate\Filament\Resources\Grants\GrantResource;
use Filament\Resources\Pages\ListRecords;

final class ListGrants extends ListRecords
{
    protected static string $resource = GrantResource::class;
}
