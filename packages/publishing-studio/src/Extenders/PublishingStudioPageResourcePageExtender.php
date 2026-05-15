<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Extenders;

use Capell\Admin\Contracts\Extenders\PageResourcePageExtender;
use Capell\PublishingStudio\Filament\Resources\Pages\Pages\PageVersionHistoryPage;
use Filament\Resources\Pages\PageRegistration;

class PublishingStudioPageResourcePageExtender implements PageResourcePageExtender
{
    /** @return array<string, PageRegistration> */
    public function getPages(): array
    {
        return [
            'history' => PageVersionHistoryPage::route('/{record}/history'),
        ];
    }
}
