<?php

declare(strict_types=1);

use Capell\Backup\Tests\BackupTestCase;

pest()->extend(BackupTestCase::class)->group('backup')->in(__DIR__);
