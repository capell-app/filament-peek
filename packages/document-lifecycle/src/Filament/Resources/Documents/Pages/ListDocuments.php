<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Filament\Resources\Documents\Pages;

use Capell\DocumentLifecycle\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\ListRecords;

final class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;
}
