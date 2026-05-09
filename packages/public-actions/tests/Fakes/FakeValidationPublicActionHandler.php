<?php

declare(strict_types=1);

namespace Capell\PublicActions\Tests\Fakes;

use Capell\PublicActions\Contracts\PublicActionHandler;
use Capell\PublicActions\Data\PublicActionResultData;
use Capell\PublicActions\Data\PublicActionSubmissionData;
use Illuminate\Validation\ValidationException;

final class FakeValidationPublicActionHandler implements PublicActionHandler
{
    public function handle(PublicActionSubmissionData $submission): PublicActionResultData
    {
        throw ValidationException::withMessages([
            'email' => 'The email field is required.',
        ]);
    }
}
