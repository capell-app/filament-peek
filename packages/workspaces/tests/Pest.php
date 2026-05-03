<?php

declare(strict_types=1);

use Capell\Workspaces\Tests\WorkspacesTestCase;

pest()->extend(WorkspacesTestCase::class)->group('workspaces')->in(__DIR__);
