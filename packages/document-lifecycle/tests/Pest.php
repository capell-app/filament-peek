<?php

declare(strict_types=1);

use Capell\DocumentLifecycle\Tests\DocumentLifecycleTestCase;

require_once __DIR__ . '/DocumentLifecycleTestCase.php';

pest()->extend(DocumentLifecycleTestCase::class)->group('document-lifecycle')->in(__DIR__);
