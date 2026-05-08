<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Enums\IdentityMode;
use Capell\AccessGate\Models\Grant;
use Capell\AccessGate\Models\Registration;
use Capell\AccessGate\Notifications\AccessApprovedNotification;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

final class SendAccessGateApprovedNotificationAction
{
    use AsAction;

    public function __construct(
        private readonly CreateAccessGateClaimTokenAction $createClaimToken,
    ) {}

    public function handle(Registration $registration, Grant $grant): void
    {
        if ($registration->email === '') {
            return;
        }

        $area = $grant->area()->firstOrFail();
        $claimUrl = null;

        if ($area->identity_mode === IdentityMode::GuestLink || $area->identity_mode === IdentityMode::Hybrid) {
            $issuedClaimToken = $this->createClaimToken->handle(
                grant: $grant,
                expiresAt: now()->addMinutes((int) config('access-gate.claim_token_ttl_minutes', 10080)),
            );

            $claimUrl = route('capell-access-gate.claim', ['token' => $issuedClaimToken->plainTextToken]);
        }

        Notification::route('mail', $registration->email)
            ->notify(new AccessApprovedNotification($area, $claimUrl));
    }
}
