<?php

declare(strict_types=1);

use Capell\ThemeStudio\Admin\Tests\ThemeStudioAdminTestCase;

pest()->extend(ThemeStudioAdminTestCase::class)->group('theme-studio-admin')->in(__DIR__);
