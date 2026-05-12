<?php

declare(strict_types=1);

namespace Capell\Diagnostics\Providers;

use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Spatie\LaravelPackageTools\Package;

final class DiagnosticsServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-diagnostics';

    public static string $packageName = 'capell-app/diagnostics';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasTranslations()
            ->hasViews(self::$name);
    }

    public function registeringPackage(): void {}
}
