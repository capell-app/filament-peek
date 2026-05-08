<?php

declare(strict_types=1);

namespace Capell\AccessGate\Filament\Resources\BrowserTokens\Pages;

use Capell\AccessGate\Filament\Resources\BrowserTokens\BrowserTokenResource;
use Filament\Resources\Pages\ListRecords;

final class ListBrowserTokens extends ListRecords
{
    protected static string $resource = BrowserTokenResource::class;
}
