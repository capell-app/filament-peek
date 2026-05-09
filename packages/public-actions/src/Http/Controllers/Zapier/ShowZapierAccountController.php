<?php

declare(strict_types=1);

namespace Capell\PublicActions\Http\Controllers\Zapier;

use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ShowZapierAccountController
{
    public function __invoke(Request $request): JsonResponse
    {
        $token = $request->attributes->get('public_action_integration_token');
        abort_unless($token instanceof PublicActionIntegrationToken, 401);

        return response()->json([
            'id' => (string) $token->getKey(),
            'name' => $token->name,
            'provider' => $token->provider->value,
            'site_id' => $token->site_id,
            'site_name' => $token->site?->name,
        ]);
    }
}
