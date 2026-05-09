<?php

declare(strict_types=1);

use Capell\EmailStudio\Tests\EmailStudioTestCase;

pest()->extend(EmailStudioTestCase::class)->group('email-studio')->in(__DIR__);
