<?php

declare(strict_types=1);

namespace Capell\PublicActions\Contracts;

use Capell\PublicActions\Data\PublicActionResultData;
use Capell\PublicActions\Data\PublicActionSubmissionData;

interface PublicActionHandler
{
    public function handle(PublicActionSubmissionData $submission): PublicActionResultData;
}
