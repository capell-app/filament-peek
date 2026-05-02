<?php

declare(strict_types=1);

namespace Capell\Deployments\Contracts;

use Capell\Deployments\Data\ComposerRequirementData;
use Capell\Deployments\Data\PublishComposerChangeResultData;

interface PublishesComposerChanges
{
    public function publish(ComposerRequirementData $requirement): PublishComposerChangeResultData;
}
