<?php

declare(strict_types=1);

namespace Capell\AccessGate\Filament\Resources\AccessAreas\Pages;

use Capell\AccessGate\Filament\Resources\AccessAreas\AccessAreaResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateAccessArea extends CreateRecord
{
    protected static string $resource = AccessAreaResource::class;
}
