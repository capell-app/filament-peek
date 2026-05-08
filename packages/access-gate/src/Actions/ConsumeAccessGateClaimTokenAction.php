<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Data\IssuedAccessGateTokenData;
use Capell\AccessGate\Enums\ClaimTokenStatus;
use Capell\AccessGate\Enums\EventType;
use Capell\AccessGate\Enums\RegistrationStatus;
use Capell\AccessGate\Models\ClaimToken;
use Illuminate\Support\Facades\DB;
use LogicException;
use Lorisleiva\Actions\Concerns\AsAction;

final class ConsumeAccessGateClaimTokenAction
{
    use AsAction;

    public function __construct(
        private readonly CreateAccessGateBrowserTokenAction $createBrowserToken,
        private readonly EnsureAccessGateGrantCanIssueTokenAction $ensureTokenIssuableGrant,
        private readonly RecordEventAction $recordEvent,
    ) {}

    public function handle(string $plainTextToken): ?IssuedAccessGateTokenData
    {
        return DB::transaction(function () use ($plainTextToken): ?IssuedAccessGateTokenData {
            $claimToken = ClaimToken::query()
                ->where('token_hash', hash('sha256', $plainTextToken))
                ->where('status', ClaimTokenStatus::Active->value)
                ->whereNull('consumed_at')
                ->lockForUpdate()
                ->first();

            if ($claimToken === null || $this->isExpired($claimToken) || $claimToken->grant === null) {
                return null;
            }

            try {
                $grant = $this->ensureTokenIssuableGrant->handle($claimToken->grant);
            } catch (LogicException) {
                return null;
            }

            $claimToken->forceFill([
                'status' => ClaimTokenStatus::Claimed,
                'consumed_at' => now(),
            ])->save();

            if ($claimToken->registration !== null) {
                $claimToken->registration->forceFill([
                    'status' => RegistrationStatus::Claimed,
                    'claimed_at' => now(),
                ])->save();
            }

            $issuedBrowserToken = $this->createBrowserToken->handle($grant);

            $this->recordEvent->handle(
                type: EventType::ClaimTokenClaimed,
                claimToken: $claimToken,
                grant: $grant,
                browserToken: $issuedBrowserToken->token,
            );

            return $issuedBrowserToken;
        });
    }

    private function isExpired(ClaimToken $claimToken): bool
    {
        return $claimToken->expires_at !== null && $claimToken->expires_at->isPast();
    }
}
