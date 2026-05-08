<?php

declare(strict_types=1);

namespace Capell\AccessGate\Filament\Resources\Registrations\Pages;

use Capell\AccessGate\Filament\Resources\Registrations\RegistrationResource;
use Filament\Resources\Pages\ListRecords;

final class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;
}
