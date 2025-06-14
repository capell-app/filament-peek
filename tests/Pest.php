<?php

declare(strict_types=1);

use Capell\Tests\packages\ArchTestCase;
use Capell\Tests\packages\blog\BlogTestCase;

pest()->extends(ArchTestCase::class)->in(__DIR__.DIRECTORY_SEPARATOR.'packages'.DIRECTORY_SEPARATOR.'PackagesTest.php');
pest()->extends(BlogTestCase::class)->in(__DIR__.DIRECTORY_SEPARATOR.'packages'.DIRECTORY_SEPARATOR.'blog');
