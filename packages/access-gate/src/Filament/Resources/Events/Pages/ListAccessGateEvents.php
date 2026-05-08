<?php

declare(strict_types=1);

namespace Capell\AccessGate\Filament\Resources\Events\Pages;

use Capell\AccessGate\Filament\Resources\Events\AccessGateEventResource;
use Filament\Resources\Pages\ListRecords;

final class ListAccessGateEvents extends ListRecords
{
    protected static string $resource = AccessGateEventResource::class;
}
