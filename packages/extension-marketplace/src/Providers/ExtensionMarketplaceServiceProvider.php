<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Providers;

use Capell\Admin\Facades\CapellAdmin;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\ExtensionMarketplace\Filament\Pages\ExtensionMarketplacePage;
use Composer\InstalledVersions;
use Spatie\LaravelPackageTools\Package;

class ExtensionMarketplaceServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-extension-marketplace';

    public static string $packageName = 'capell-app/extension-marketplace';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasConfigFile()
            ->hasRoute('marketplace')
            ->hasViews(self::$name)
            ->hasTranslations();
    }

    public function registeringPackage(): void
    {
        CapellCore::registerPackage(
            static::$packageName,
            type: static::getType(),
            serviceProviderClass: static::class,
            path: realpath(__DIR__ . '/../..'),
            version: $this->getVersion(),
            description: fn (): string => __('capell-extension-marketplace::package.description'),
        );

        if (config('capell-extension-marketplace.enabled', true)) {
            CapellAdmin::registerPage(ExtensionMarketplacePage::class);
        }
    }

    private function getVersion(): string
    {
        if (! class_exists(InstalledVersions::class)) {
            return 'dev';
        }

        if (! InstalledVersions::isInstalled(static::$packageName)) {
            return 'dev';
        }

        return InstalledVersions::getPrettyVersion(static::$packageName) ?? 'dev';
    }
}
