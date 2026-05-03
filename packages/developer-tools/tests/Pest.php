<?php

declare(strict_types=1);

use Capell\DeveloperTools\Tests\DeveloperToolsTestCase;

pest()->extend(DeveloperToolsTestCase::class)->group('developer-tools')->in(__DIR__);
