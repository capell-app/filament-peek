<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Data;

use Spatie\LaravelData\Data;

final class HeartbeatResultData extends Data
{
    /**
     * @param  array<int, UpdateNoticeData>  $updates
     * @param  array<int, AdvisoryNoticeData>  $advisories
     */
    public function __construct(
        public readonly string $instanceId,
        public readonly ?string $signingSecret,
        public readonly array $updates,
        public readonly array $advisories,
        public readonly ?string $checkedAt = null,
        public readonly ?string $capellVersion = null,
        public readonly ?string $responseId = null,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromApiResponse(array $payload): self
    {
        $updates = array_map(
            UpdateNoticeData::fromApiResponse(...),
            is_array($payload['updates'] ?? null) ? $payload['updates'] : [],
        );

        $advisories = array_map(
            AdvisoryNoticeData::fromApiResponse(...),
            is_array($payload['advisories'] ?? null) ? $payload['advisories'] : [],
        );

        return new self(
            instanceId: $payload['instance_id'],
            signingSecret: isset($payload['signing_secret']) && is_string($payload['signing_secret'])
                ? $payload['signing_secret']
                : null,
            updates: $updates,
            advisories: $advisories,
            checkedAt: isset($payload['checked_at']) ? (string) $payload['checked_at'] : null,
            capellVersion: isset($payload['capell_version']) ? (string) $payload['capell_version'] : null,
            responseId: isset($payload['response_id']) ? (string) $payload['response_id'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'checked_at' => $this->checkedAt,
            'capell_version' => $this->capellVersion,
            'updates' => array_map(fn (UpdateNoticeData $update): array => $update->toArray(), $this->updates),
            'advisories' => array_map(fn (AdvisoryNoticeData $advisory): array => $advisory->toArray(), $this->advisories),
            'response_id' => $this->responseId,
            'instance_id' => $this->instanceId,
        ], fn (mixed $value): bool => $value !== null);
    }
}
