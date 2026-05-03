<?php

declare(strict_types=1);

use Capell\HtmlMinify\Tests\HtmlMinifyTestCase;

pest()->extend(HtmlMinifyTestCase::class)->group('html-minify')->in(__DIR__);
