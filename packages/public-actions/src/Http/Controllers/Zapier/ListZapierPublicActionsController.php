<?php

declare(strict_types=1);

namespace Capell\PublicActions\Http\Controllers\Zapier;

use Capell\PublicActions\Enums\PublicActionIntegrationTokenAbility;
use Capell\PublicActions\Enums\PublicActionStatus;
use Capell\PublicActions\Models\PublicAction;
use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListZapierPublicActionsController
{
    public function __invoke(Request $request): JsonResponse
    {
        $token = $this->token($request);

        abort_unless($token->hasAbility(PublicActionIntegrationTokenAbility::ListActions), 403);

        $actions = PublicAction::query()
            ->where('status', PublicActionStatus::Active)
            ->when($token->site_id !== null, fn (Builder $query): Builder => $query->where(
                fn (Builder $builder): Builder => $builder->where('site_id', $token->site_id)->orWhereNull('site_id'),
            ))
            ->where(fn (Builder $query): Builder => $query
                ->where('settings->zapier_enabled', true)
                ->orWhere('settings->api_enabled', true))
            ->orderBy('name')
            ->get(['id', 'key', 'name'])
            ->map(fn (PublicAction $action): array => [
                'id' => (string) $action->getKey(),
                'key' => $action->key,
                'name' => $action->name,
            ])
            ->values();

        return response()->json(['actions' => $actions]);
    }

    private function token(Request $request): PublicActionIntegrationToken
    {
        $token = $request->attributes->get('public_action_integration_token');
        abort_unless($token instanceof PublicActionIntegrationToken, 401);

        return $token;
    }
}
