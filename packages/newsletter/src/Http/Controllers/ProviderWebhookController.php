<?php

declare(strict_types=1);

namespace Capell\Newsletter\Http\Controllers;

use Capell\Newsletter\Actions\HandleProviderWebhookAction;
use Capell\Newsletter\Models\ProviderConnection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderWebhookController
{
    public function __invoke(Request $request, int|string $providerConnection): JsonResponse
    {
        $providerConnection = ProviderConnection::query()->findOrFail($providerConnection);

        try {
            $subscriber = HandleProviderWebhookAction::run($providerConnection, $request);
        } catch (AuthorizationException) {
            return response()->json([
                'ok' => false,
            ], 403);
        }

        return response()->json([
            'ok' => $subscriber !== null,
        ], $subscriber === null ? 422 : 200);
    }
}
