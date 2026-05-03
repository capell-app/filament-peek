<?php

declare(strict_types=1);

use Capell\Redirects\Tests\RedirectsTestCase;

pest()->extend(RedirectsTestCase::class)->group('redirects')->in(__DIR__);
