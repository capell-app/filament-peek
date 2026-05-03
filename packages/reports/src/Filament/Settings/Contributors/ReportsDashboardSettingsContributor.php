<?php

declare(strict_types=1);

namespace Capell\Reports\Filament\Settings\Contributors;

use Capell\Admin\Contracts\DashboardSettingsContributor;

final class ReportsDashboardSettingsContributor implements DashboardSettingsContributor
{
    /**
     * @return list<array{key: string, label: string, group: string}>
     */
    public function settingsKeys(): array
    {
        return [
            [
                'key' => 'publishing_trend',
                'label' => __('capell-reports::dashboard.widget_publishing_trend'),
                'group' => __('capell-reports::dashboard.group_reports'),
            ],
            [
                'key' => 'content_health',
                'label' => __('capell-reports::dashboard.widget_content_health'),
                'group' => __('capell-reports::dashboard.group_reports'),
            ],
        ];
    }
}
