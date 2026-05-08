<?php

declare(strict_types=1);

namespace Capell\AccessGate\Enums;

use Capell\AccessGate\Filament\Resources\AccessAreas\AccessAreaResource;
use Capell\AccessGate\Filament\Resources\BrowserTokens\BrowserTokenResource;
use Capell\AccessGate\Filament\Resources\ClaimTokens\ClaimTokenResource;
use Capell\AccessGate\Filament\Resources\Events\AccessGateEventResource;
use Capell\AccessGate\Filament\Resources\Grants\GrantResource;
use Capell\AccessGate\Filament\Resources\Registrations\RegistrationResource;

enum ResourceEnum: string
{
    case AccessArea = AccessAreaResource::class;
    case Registration = RegistrationResource::class;
    case Grant = GrantResource::class;
    case BrowserToken = BrowserTokenResource::class;
    case ClaimToken = ClaimTokenResource::class;
    case Event = AccessGateEventResource::class;
}
