<?php

declare(strict_types=1);

use Capell\Campaigns\Tests\CampaignsTestCase;

pest()->extend(CampaignsTestCase::class)->group('campaigns')->in(__DIR__);
