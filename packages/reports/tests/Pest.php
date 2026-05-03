<?php

declare(strict_types=1);

use Capell\Reports\Tests\ReportsTestCase;

pest()->extend(ReportsTestCase::class)->group('reports')->in(__DIR__);
