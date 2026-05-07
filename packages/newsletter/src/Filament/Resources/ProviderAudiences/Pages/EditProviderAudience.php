<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\ProviderAudiences\Pages;

use Capell\Newsletter\Filament\Resources\ProviderAudiences\ProviderAudienceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProviderAudience extends EditRecord
{
    protected static string $resource = ProviderAudienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
