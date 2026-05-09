<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Tests\Support;

use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Providers\FrontendServiceProvider;
use Capell\SeoSuite\Providers\SeoSuiteServiceProvider;
use Capell\Tests\AbstractTestCase;
use Livewire\LivewireServiceProvider;

class AiDiscoveryIntegrationTestCase extends AbstractTestCase
{
    protected function getPackageServiceName(): string
    {
        return 'capell-seo-suite';
    }

    /**
     * @return class-string[]
     */
    protected function getPackageProviders(mixed $app): array
    {
        return [
            ...parent::getDefaultPackageProviders(),
            FrontendServiceProvider::class,
            SeoSuiteServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp(mixed $app): void
    {
        parent::getEnvironmentSetUp($app);

        CapellCore::registerPackage(
            FrontendServiceProvider::$packageName,
            path: realpath(__DIR__ . '/../../../../../capell-4/packages/frontend'),
        );
        CapellCore::forcePackageInstalled(FrontendServiceProvider::$packageName);
        CapellCore::registerPackage(
            SeoSuiteServiceProvider::$packageName,
            path: dirname(__DIR__, 2),
        );
        CapellCore::forcePackageInstalled(SeoSuiteServiceProvider::$packageName);
    }
}
