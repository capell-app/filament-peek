<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Enums\BrowserTokenStatus;
use Capell\AccessGate\Enums\EventType;
use Capell\AccessGate\Enums\GrantStatus;
use Capell\AccessGate\Models\Grant;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class RevokeAccessGateGrantAction
{
    use AsAction;

    public function __construct(
        private readonly RecordEventAction $recordEvent,
    ) {}

    public function handle(Grant $grant, ?int $revokedByUserId = null): Grant
    {
        return DB::transaction(function () use ($grant, $revokedByUserId): Grant {
            $lockedGrant = Grant::query()
                ->whereKey($grant->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedGrant->status !== GrantStatus::Revoked) {
                $lockedGrant->forceFill([
                    'status' => GrantStatus::Revoked,
                    'revoked_at' => now(),
                ])->save();
            }

            $lockedGrant->browserTokens()
                ->where('status', BrowserTokenStatus::Active->value)
                ->update([
                    'status' => BrowserTokenStatus::Revoked->value,
                    'revoked_at' => now(),
                ]);

            $this->recordEvent->handle(
                type: EventType::GrantRevoked,
                grant: $lockedGrant,
                userId: $revokedByUserId,
                payload: [
                    'revoked_by_user_id' => $revokedByUserId,
                ],
            );

            return $lockedGrant;
        });
    }
}
