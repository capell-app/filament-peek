<?php

declare(strict_types=1);

use Capell\ContentBlocks\Tests\ContentBlockRenderingTestCase;

pest()->extend(ContentBlockRenderingTestCase::class)->group('content-blocks')->in(__DIR__ . '/Unit');
pest()->extend(ContentBlockRenderingTestCase::class)->group('content-blocks')->in(__DIR__ . '/Feature');
