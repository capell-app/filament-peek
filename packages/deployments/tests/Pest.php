<?php

declare(strict_types=1);

use Capell\Deployments\Tests\TestCase;

pest()->extend(TestCase::class)->group('deployments')->in(__DIR__);
