<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Filament\Resources\Documents\Pages;

use Capell\DocumentLifecycle\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\EditRecord;

final class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;
}
