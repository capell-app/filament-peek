<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Support;

use RuntimeException;

final class MarketplaceBaseUrl
{
    public static function resolve(): string
    {
        $baseUrl = config('capell-extension-marketplace.marketplace.base_url');

        throw_if(! is_string($baseUrl) || $baseUrl === '', RuntimeException::class, 'The marketplace base URL must be configured before contacting Marketplace.');

        return rtrim($baseUrl, '/');
    }
}
