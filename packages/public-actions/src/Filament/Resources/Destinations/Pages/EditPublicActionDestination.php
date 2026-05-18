<?php

declare(strict_types=1);

namespace Capell\PublicActions\Filament\Resources\Destinations\Pages;

use Capell\PublicActions\Filament\Resources\Destinations\PublicActionDestinationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditPublicActionDestination extends EditRecord
{
    protected static string $resource = PublicActionDestinationResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
