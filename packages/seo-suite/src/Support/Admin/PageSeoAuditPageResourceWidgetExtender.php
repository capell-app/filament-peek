<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Support\Admin;

use Capell\Admin\Contracts\Extenders\PageResourceWidgetExtender;
use Capell\SeoSuite\Filament\Resources\Pages\Widgets\ListPageSeoAuditWidget;

final class PageSeoAuditPageResourceWidgetExtender implements PageResourceWidgetExtender
{
    public function getWidgets(): array
    {
        return [
            ListPageSeoAuditWidget::class,
        ];
    }
}
