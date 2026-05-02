<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Tests;

use Capell\Admin\Providers\AdminServiceProvider;
use Capell\Admin\Providers\Filament\AdminPanelProvider;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Providers\CapellServiceProvider;
use Capell\ExtensionMarketplace\Providers\ExtensionMarketplaceServiceProvider;
use Capell\Tests\AbstractTestCase;
use Livewire\LivewireServiceProvider;
use Override;

abstract class TestCase extends AbstractTestCase
{
    protected function getPackageServiceName(): string
    {
        return 'capell-extension-marketplace';
    }

    #[Override]
    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            CapellServiceProvider::class,
            AdminServiceProvider::class,
            AdminPanelProvider::class,
            LivewireServiceProvider::class,
            ExtensionMarketplaceServiceProvider::class,
        ];
    }

    #[Override]
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        CapellCore::forcePackageInstalled(AdminServiceProvider::$packageName);
        CapellCore::forcePackageInstalled(ExtensionMarketplaceServiceProvider::$packageName);
    }
}
