<?php

declare(strict_types=1);

namespace Capell\Analytics\Providers;

use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Spatie\LaravelPackageTools\Package;

class AnalyticsServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-analytics';

    public static string $packageName = 'capell-app/analytics';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasConfigFile('capell-analytics')
            ->hasTranslations()
            ->hasViews(self::$name)
            ->hasRoute('web');
    }

    public function registeringPackage(): void
    {
        $this->app->register(AdminServiceProvider::class);
    }

    public function packageRegistered(): void
    {
        $this->registerPackageMetadata();
    }

    private function registerPackageMetadata(): self
    {
        CapellCore::registerPackage(
            self::$packageName,
            type: self::getType(),
            serviceProviderClass: self::class,
            path: realpath(__DIR__ . '/../..'),
            version: CapellCore::getInstalledPrettyVersion(self::$packageName),
            description: fn (): string => __('capell-analytics::package.description'),
        );

        return $this;
    }
}
