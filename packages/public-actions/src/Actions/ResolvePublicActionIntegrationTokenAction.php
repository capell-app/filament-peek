<?php

declare(strict_types=1);

namespace Capell\PublicActions\Actions;

use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Lorisleiva\Actions\Concerns\AsAction;

final class ResolvePublicActionIntegrationTokenAction
{
    use AsAction;

    public function handle(?string $plainTextToken): ?PublicActionIntegrationToken
    {
        if ($plainTextToken === null || $plainTextToken === '') {
            return null;
        }

        $token = PublicActionIntegrationToken::query()
            ->where('token_hash', PublicActionIntegrationToken::hashPlainTextToken($plainTextToken))
            ->whereNull('revoked_at')
            ->first();

        if (! $token instanceof PublicActionIntegrationToken) {
            return null;
        }

        $token->forceFill(['last_used_at' => now()])->save();

        return $token;
    }
}
