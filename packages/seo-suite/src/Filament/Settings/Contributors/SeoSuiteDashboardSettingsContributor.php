<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Filament\Settings\Contributors;

use Capell\Admin\Contracts\DashboardSettingsContributor;

final class SeoSuiteDashboardSettingsContributor implements DashboardSettingsContributor
{
    /**
     * @return list<array{key: string, label: string, group: string}>
     */
    public function settingsKeys(): array
    {
        return [
            [
                'key' => 'seo_search_console_overview',
                'label' => __('capell-seo-suite::dashboard.search_console_overview'),
                'group' => __('capell-seo-suite::dashboard.group'),
            ],
            [
                'key' => 'seo_top_search_pages',
                'label' => __('capell-seo-suite::dashboard.top_search_pages'),
                'group' => __('capell-seo-suite::dashboard.group'),
            ],
            [
                'key' => 'seo_search_movement',
                'label' => __('capell-seo-suite::dashboard.search_movement'),
                'group' => __('capell-seo-suite::dashboard.group'),
            ],
            [
                'key' => 'seo_opportunities',
                'label' => __('capell-seo-suite::dashboard.seo_opportunities'),
                'group' => __('capell-seo-suite::dashboard.group'),
            ],
            [
                'key' => 'seo_ai_discovery_coverage',
                'label' => __('capell-seo-suite::dashboard.ai_discovery_coverage'),
                'group' => __('capell-seo-suite::dashboard.group'),
            ],
        ];
    }
}
