<?php

declare(strict_types=1);

namespace Capell\Newsletter\Data;

use Capell\Newsletter\Enums\SubscriberStatus;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ProviderWebhookEventData extends Data
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $email,
        public SubscriberStatus $status,
        public string $eventType,
        public ?string $remoteId = null,
        public array $payload = [],
    ) {}
}
