<?php

declare(strict_types=1);

namespace Capell\PublicActions\Tests\Fakes;

use Capell\PublicActions\Contracts\PublicActionHandler;
use Capell\PublicActions\Data\PublicActionResultData;
use Capell\PublicActions\Data\PublicActionSubmissionData;

final class FakePublicActionHandler implements PublicActionHandler
{
    public function handle(PublicActionSubmissionData $submission): PublicActionResultData
    {
        return new PublicActionResultData(
            success: true,
            message: $submission->actionKey,
        );
    }
}
