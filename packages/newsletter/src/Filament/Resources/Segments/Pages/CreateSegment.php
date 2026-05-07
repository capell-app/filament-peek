<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\Segments\Pages;

use Capell\Newsletter\Filament\Resources\Segments\SegmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSegment extends CreateRecord
{
    protected static string $resource = SegmentResource::class;
}
