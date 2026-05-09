<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\PublicActions\Providers\PublicActionsServiceProvider;

it('registers the public actions package metadata and config', function (): void {
    $package = CapellCore::getPackage(PublicActionsServiceProvider::$packageName);

    expect($package->name)->toBe(PublicActionsServiceProvider::$packageName)
        ->and(config('capell-public-actions.tables.actions'))->toBe('public_actions')
        ->and(config('capell-public-actions.route_prefix'))->toBe('actions')
        ->and(config('capell-public-actions.adapters.presets.zapier.adapter'))->toBe('http_webhook');
});
