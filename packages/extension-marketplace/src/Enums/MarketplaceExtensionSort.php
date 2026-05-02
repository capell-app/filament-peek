<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Enums;

use Filament\Support\Contracts\HasLabel;

enum MarketplaceExtensionSort: string implements HasLabel
{
    case FeaturedLatest = 'featured_latest';
    case Latest = 'latest';
    case Popular = 'popular';
    case PriceLow = 'price_low';
    case PriceHigh = 'price_high';
    case Name = 'name';

    public function getLabel(): string
    {
        return (string) __('capell-extension-marketplace::marketplace.sort.' . $this->value);
    }
}
