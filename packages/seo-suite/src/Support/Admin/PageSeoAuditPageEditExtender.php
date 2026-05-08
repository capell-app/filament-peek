<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Support\Admin;

use Capell\Admin\Contracts\Extenders\PageEditExtender;
use Capell\SeoSuite\Filament\Widgets\EditPageSeoAuditWidget;

class PageSeoAuditPageEditExtender implements PageEditExtender
{
    /**
     * @return array<int, mixed>
     */
    public function getFormActions(): array
    {
        return [];
    }

    /**
     * @return array<int, mixed>
     */
    public function getHeaderWidgets(): array
    {
        return [
            EditPageSeoAuditWidget::class,
        ];
    }
}
