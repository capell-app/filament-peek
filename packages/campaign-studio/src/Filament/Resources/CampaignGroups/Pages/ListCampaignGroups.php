<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Filament\Resources\CampaignGroups\Pages;

use Capell\CampaignStudio\Filament\Resources\CampaignGroups\CampaignGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListCampaignGroups extends ListRecords
{
    protected static string $resource = CampaignGroupResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
