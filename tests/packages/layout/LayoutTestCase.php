<?php

declare(strict_types=1);

namespace Capell\Packages\Tests\packages\layout;

use Capell\Layout\LayoutServiceProvider;
use Capell\Tests\packages\AbstractTestCase;
use Capella\Layout\LayoutManager;

class LayoutTestCase extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadPackageMigrations(LayoutManager::getMigrations());
    }

    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            LayoutServiceProvider::class,
        ];
    }
}
