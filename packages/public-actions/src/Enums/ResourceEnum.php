<?php

declare(strict_types=1);

namespace Capell\PublicActions\Enums;

use Capell\PublicActions\Filament\Resources\DispatchAttempts\PublicActionDispatchAttemptResource;
use Capell\PublicActions\Filament\Resources\IntegrationTokens\PublicActionIntegrationTokenResource;
use Capell\PublicActions\Filament\Resources\PublicActions\PublicActionResource;
use Capell\PublicActions\Filament\Resources\Submissions\PublicActionSubmissionResource;

enum ResourceEnum: string
{
    case PublicAction = PublicActionResource::class;
    case PublicActionSubmission = PublicActionSubmissionResource::class;
    case PublicActionDispatchAttempt = PublicActionDispatchAttemptResource::class;
    case PublicActionIntegrationToken = PublicActionIntegrationTokenResource::class;
}
