<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Tests\Fixtures\Autoload;

use Capell\PublishingStudio\Checks\PublishCheck;
use Capell\PublishingStudio\Checks\PublishCheckResult;
use Capell\PublishingStudio\Checks\PublishCheckSeverity;
use Capell\PublishingStudio\Models\Workspace;

class FixturePassingCheck implements PublishCheck
{
    public function identifier(): string
    {
        return 'fixture-pass';
    }

    public function label(): string
    {
        return 'Fixture pass';
    }

    public function run(Workspace $workspace): PublishCheckResult
    {
        return new PublishCheckResult(
            identifier: $this->identifier(),
            label: $this->label(),
            severity: PublishCheckSeverity::Info,
        );
    }
}
