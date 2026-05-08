<?php

declare(strict_types=1);

use Capell\Newsletter\Tests\NewsletterTestCase;

pest()->extend(NewsletterTestCase::class)->group('newsletter')->in(__DIR__);
