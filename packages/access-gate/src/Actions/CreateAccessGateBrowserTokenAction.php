<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Data\IssuedAccessGateTokenData;
use Capell\AccessGate\Enums\BrowserTokenStatus;
use Capell\AccessGate\Enums\EventType;
use Capell\AccessGate\Enums\TokenPolicy;
use Capell\AccessGate\Models\BrowserToken;
use Capell\AccessGate\Models\Grant;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateAccessGateBrowserTokenAction
{
    use AsAction;

    public function __construct(
        private readonly EnsureAccessGateGrantCanIssueTokenAction $ensureTokenIssuableGrant,
        private readonly RecordEventAction $recordEvent,
    ) {}

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function handle(Grant $grant, array $metadata = []): IssuedAccessGateTokenData
    {
        $grant = $this->ensureTokenIssuableGrant->handle($grant);

        $this->revokeExistingTokensWhenNeeded($grant);

        $plainTextToken = Str::random(64);

        $browserToken = BrowserToken::query()->create([
            'access_area_id' => $grant->access_area_id,
            'grant_id' => $grant->getKey(),
            'token_hash' => hash('sha256', $plainTextToken),
            'status' => BrowserTokenStatus::Active,
            'ip_hash' => $metadata['ip_hash'] ?? null,
            'user_agent' => $metadata['user_agent'] ?? null,
            'expires_at' => $grant->expires_at ?? now()->addMinutes((int) config('access-gate.cookies.browser_token.ttl_minutes', 259200)),
            'last_used_at' => now(),
            'revoked_at' => null,
            'metadata' => $metadata,
        ]);

        $this->recordEvent->handle(
            type: EventType::BrowserTokenCreated,
            browserToken: $browserToken,
            grant: $grant,
        );

        return new IssuedAccessGateTokenData($plainTextToken, $browserToken);
    }

    private function revokeExistingTokensWhenNeeded(Grant $grant): void
    {
        if ($grant->area?->token_policy !== TokenPolicy::SingleActiveBrowserToken) {
            return;
        }

        BrowserToken::query()
            ->where('grant_id', $grant->getKey())
            ->where('status', BrowserTokenStatus::Active)
            ->update([
                'status' => BrowserTokenStatus::Revoked->value,
                'revoked_at' => now(),
            ]);
    }
}
