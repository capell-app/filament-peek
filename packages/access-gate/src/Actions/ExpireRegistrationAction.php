<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Enums\RegistrationStatus;
use Capell\AccessGate\Models\Registration;
use Illuminate\Support\Facades\DB;
use LogicException;
use Lorisleiva\Actions\Concerns\AsAction;

final class ExpireRegistrationAction
{
    use AsAction;

    public function handle(Registration $registration): Registration
    {
        return DB::transaction(function () use ($registration): Registration {
            $lockedRegistration = Registration::query()
                ->whereKey($registration->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRegistration->status === RegistrationStatus::Expired) {
                return $lockedRegistration;
            }

            if ($lockedRegistration->status === RegistrationStatus::Claimed) {
                throw new LogicException('Claimed access gate registrations cannot be expired.');
            }

            $lockedRegistration->forceFill([
                'status' => RegistrationStatus::Expired,
                'expired_at' => now(),
            ])->save();

            return $lockedRegistration;
        });
    }
}
