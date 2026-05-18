<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Filament\Resources\CampaignGroups\Pages;

use Capell\CampaignStudio\Filament\Resources\CampaignGroups\CampaignGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditCampaignGroup extends EditRecord
{
    protected static string $resource = CampaignGroupResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
