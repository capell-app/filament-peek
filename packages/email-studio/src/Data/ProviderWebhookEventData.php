<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Data;

use Spatie\LaravelData\Data;

class ProviderWebhookEventData extends Data
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $provider,
        public string $eventType,
        public ?string $providerMessageId = null,
        public ?string $recipientEmail = null,
        public ?string $idempotencyKey = null,
        public array $payload = [],
    ) {}
}
