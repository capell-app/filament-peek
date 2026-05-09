<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Data;

use Spatie\LaravelData\Data;

class InboundEmailReplyData extends Data
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $provider,
        public ?string $providerMessageId,
        public string $fromEmail,
        public ?string $fromName = null,
        public ?string $subject = null,
        public ?string $textBody = null,
        public ?string $htmlBody = null,
        public array $payload = [],
    ) {}
}
