<?php

declare(strict_types=1);

namespace Capell\PublicActions\Actions;

use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Lorisleiva\Actions\Concerns\AsAction;

final class RevokePublicActionIntegrationTokenAction
{
    use AsAction;

    public function handle(PublicActionIntegrationToken $token): PublicActionIntegrationToken
    {
        $token->forceFill(['revoked_at' => now()])->save();

        return $token;
    }
}
