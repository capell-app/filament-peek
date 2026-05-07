<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\Segments\Pages;

use Capell\Newsletter\Filament\Resources\Segments\SegmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSegment extends EditRecord
{
    protected static string $resource = SegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
