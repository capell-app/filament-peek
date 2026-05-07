<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\SyncAttempts\Pages;

use Capell\Newsletter\Filament\Resources\SyncAttempts\SyncAttemptResource;
use Filament\Resources\Pages\ListRecords;

class ListSyncAttempts extends ListRecords
{
    protected static string $resource = SyncAttemptResource::class;
}
