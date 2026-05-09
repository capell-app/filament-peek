<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\DispatchAttempts\Pages;

use Capell\PublicActions\Filament\Resources\DispatchAttempts\PublicActionDispatchAttemptResource;
use Filament\Resources\Pages\ListRecords;

final class ListPublicActionDispatchAttempts extends ListRecords
{
    protected static string $resource = PublicActionDispatchAttemptResource::class;
}
