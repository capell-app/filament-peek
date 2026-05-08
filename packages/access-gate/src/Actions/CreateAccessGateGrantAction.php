<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Enums\GrantStatus;
use Capell\AccessGate\Enums\GrantSubjectType;
use Capell\AccessGate\Models\Area;
use Capell\AccessGate\Models\Grant;
use Capell\AccessGate\Models\Registration;
use Carbon\CarbonInterface;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateAccessGateGrantAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function handle(
        Area $area,
        GrantSubjectType $subjectType,
        ?Registration $registration = null,
        ?int $userId = null,
        ?string $email = null,
        ?CarbonInterface $startsAt = null,
        ?CarbonInterface $expiresAt = null,
        array $metadata = [],
    ): Grant {
        $this->assertSubjectShape($subjectType, $userId, $email);

        return Grant::query()->firstOrCreate([
            'access_area_id' => $area->getKey(),
            'registration_id' => $registration?->getKey(),
            'subject_type' => $subjectType->value,
            'subject_id' => $subjectType === GrantSubjectType::User ? (string) $userId : strtolower((string) $email),
        ], [
            'user_id' => $subjectType === GrantSubjectType::User ? $userId : null,
            'email' => $email,
            'status' => GrantStatus::Active,
            'starts_at' => $startsAt ?? now(),
            'expires_at' => $expiresAt,
            'revoked_at' => null,
            'discount_label' => $area->discount_label,
            'discount_code' => $area->discount_code,
            'discount_expires_at' => $area->discount_expires_at,
            'discount_metadata' => $area->discount_metadata ?? [],
            'metadata' => $metadata,
        ]);
    }

    private function assertSubjectShape(GrantSubjectType $subjectType, ?int $userId, ?string $email): void
    {
        if ($subjectType === GrantSubjectType::User && $userId !== null) {
            return;
        }

        if ($subjectType === GrantSubjectType::Email && is_string($email) && $email !== '') {
            return;
        }

        throw new InvalidArgumentException('Access gate grants require a matching user or email subject.');
    }
}
