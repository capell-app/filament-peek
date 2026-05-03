<?php

declare(strict_types=1);

use Capell\Tests\Packages\PackagesTestCase;

pest()->extend(PackagesTestCase::class)->group('theme-default')->in(__DIR__);
