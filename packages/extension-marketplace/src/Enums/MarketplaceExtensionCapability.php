<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Enums;

use Filament\Support\Contracts\HasLabel;

enum MarketplaceExtensionCapability: string implements HasLabel
{
    case FilamentPanel = 'filament_panel';
    case LivewireComponents = 'livewire_components';
    case LaravelRoutes = 'laravel_routes';
    case Migrations = 'migrations';
    case Settings = 'settings';
    case DashboardWidgets = 'dashboard_widgets';
    case StaticExport = 'static_export';
    case Forms = 'forms';
    case Search = 'search';

    public function getLabel(): string
    {
        return (string) __('capell-extension-marketplace::marketplace.capabilities.' . $this->value);
    }
}
