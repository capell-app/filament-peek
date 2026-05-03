<?php

declare(strict_types=1);

use Capell\Tests\Packages\PackagesTestCase;

pest()->extend(PackagesTestCase::class)->group('authentication-log')->in(__DIR__);
