<?php

declare(strict_types=1);

namespace Capell\Workspaces\Tests;

use Capell\Core\Facades\CapellCore;
use Capell\Tests\AbstractTestCase;
use Capell\Workspaces\Providers\WorkspacesServiceProvider;
use Illuminate\Foundation\Application;
use Livewire\LivewireServiceProvider;
use Override;

class WorkspacesTestCase extends AbstractTestCase
{
    #[Override]
    protected function getPackageServiceName(): string
    {
        return 'workspaces';
    }

    #[Override]
    protected function getEnvironmentSetUp(mixed $app): void
    {
        parent::getEnvironmentSetUp($app);

        CapellCore::registerPackage(
            'capell-app/workspaces',
            path: realpath(__DIR__ . '/..'),
        );

        CapellCore::forcePackageInstalled('capell-app/workspaces');
    }

    /**
     * @param  Application  $app
     * @return class-string[]
     */
    #[Override]
    protected function getPackageProviders(mixed $app): array
    {
        $providers = parent::getPackageProviders($app);

        return array_merge($providers, [
            LivewireServiceProvider::class,
            WorkspacesServiceProvider::class,
        ]);
    }
}
