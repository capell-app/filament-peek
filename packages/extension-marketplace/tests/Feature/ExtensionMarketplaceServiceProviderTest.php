<?php

declare(strict_types=1);

use Capell\Admin\Facades\CapellAdmin;
use Capell\Core\Facades\CapellCore;
use Capell\ExtensionMarketplace\Filament\Pages\ExtensionMarketplacePage;
use Capell\ExtensionMarketplace\Providers\ExtensionMarketplaceServiceProvider;

it('registers the extension marketplace package metadata', function (): void {
    expect(CapellCore::hasPackage(ExtensionMarketplaceServiceProvider::$packageName))->toBeTrue()
        ->and(CapellCore::getPackage(ExtensionMarketplaceServiceProvider::$packageName)->serviceProviderClass)
        ->toBe(ExtensionMarketplaceServiceProvider::class);
});

it('registers the extension marketplace admin page', function (): void {
    expect(CapellAdmin::getExtraPages())->toContain(ExtensionMarketplacePage::class)
        ->and(ExtensionMarketplacePage::getNavigationLabel())->toBe('Extension Marketplace');
});
