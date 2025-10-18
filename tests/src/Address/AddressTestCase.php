<?php

declare(strict_types=1);

namespace Capell\Tests\Address;

use Capell\Address\AddressServiceProvider;
use Capell\Admin\AdminServiceProvider;
use Capell\Tests\AbstractTestCase;
use Capell\Tests\Fixtures\Support\Filament\AdminPanelProvider;

class AddressTestCase extends AbstractTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            AddressServiceProvider::class,
            AdminServiceProvider::class,
            AdminPanelProvider::class,
        ];
    }
}
