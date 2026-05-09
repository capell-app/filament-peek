<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\PublicActions\Pages;

use Capell\PublicActions\Filament\Resources\PublicActions\PublicActionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListPublicActions extends ListRecords
{
    protected static string $resource = PublicActionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
