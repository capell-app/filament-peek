<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Tests\Fixtures\Autoload;

use Capell\PublishingStudio\Checks\PublishCheck;
use Capell\PublishingStudio\Checks\PublishCheckResult;
use Capell\PublishingStudio\Checks\PublishCheckSeverity;
use Capell\PublishingStudio\Models\Workspace;

class FixtureFailingCheck implements PublishCheck
{
    public function identifier(): string
    {
        return 'fixture-fail';
    }

    public function label(): string
    {
        return 'Fixture fail';
    }

    public function run(Workspace $workspace): PublishCheckResult
    {
        return new PublishCheckResult(
            identifier: $this->identifier(),
            label: $this->label(),
            severity: PublishCheckSeverity::Error,
            messages: ['oh no something broke'],
            entityRefs: [['model' => 'Page', 'uuid' => 'abc']],
        );
    }
}
