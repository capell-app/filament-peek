<?php

declare(strict_types=1);

namespace Capell\AccessGate\Filament\Resources\ClaimTokens\Pages;

use Capell\AccessGate\Filament\Resources\ClaimTokens\ClaimTokenResource;
use Filament\Resources\Pages\ListRecords;

final class ListClaimTokens extends ListRecords
{
    protected static string $resource = ClaimTokenResource::class;
}
