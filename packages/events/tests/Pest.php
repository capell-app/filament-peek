<?php

declare(strict_types=1);

use Capell\Events\Tests\EventsTestCase;

pest()->extend(EventsTestCase::class)->group('events')->in(__DIR__);
