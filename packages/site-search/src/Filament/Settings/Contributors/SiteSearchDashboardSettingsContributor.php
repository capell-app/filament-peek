<?php

declare(strict_types=1);

namespace Capell\SiteSearch\Filament\Settings\Contributors;

use Capell\Admin\Contracts\DashboardSettingsContributor;

final class SiteSearchDashboardSettingsContributor implements DashboardSettingsContributor
{
    /**
     * @return list<array{key: string, label: string, group: string}>
     */
    public function settingsKeys(): array
    {
        return [
            ['key' => 'site_search_overview', 'label' => 'Search overview', 'group' => 'Site search'],
            ['key' => 'top_searches', 'label' => 'Top searches', 'group' => 'Site search'],
            ['key' => 'trending_searches', 'label' => 'Trending searches', 'group' => 'Site search'],
            ['key' => 'zero_result_searches', 'label' => 'Zero result searches', 'group' => 'Site search'],
        ];
    }
}
