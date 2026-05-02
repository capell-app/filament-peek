<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Support;

use Illuminate\Support\Facades\Route;

final class MarketplaceWebhookUrl
{
    public static function resolve(): ?string
    {
        if (Route::has('capell.marketplace.webhook')) {
            return route('capell.marketplace.webhook', absolute: true);
        }

        $configuredWebhookUrl = config('capell-extension-marketplace.marketplace.webhook_url');

        if (is_string($configuredWebhookUrl) && $configuredWebhookUrl !== '') {
            return $configuredWebhookUrl;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        $host = parse_url($appUrl, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

        return $appUrl . '/capell/marketplace/webhook';
    }

    public static function isAvailable(): bool
    {
        return self::resolve() !== null;
    }
}
