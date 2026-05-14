<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Enums;

use Capell\DocumentLifecycle\Filament\Resources\Documents\DocumentResource;

enum ResourceEnum: string
{
    case Document = DocumentResource::class;
}
