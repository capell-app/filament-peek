<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Data;

use Spatie\LaravelData\Data;

class ProviderSendResultData extends Data
{
    /**
     * @param  array<int, string>  $recipientProviderMessageIds
     * @param  array<int, string>  $failedRecipientReasons
     */
    public function __construct(
        public bool $successful,
        public array $recipientProviderMessageIds = [],
        public array $failedRecipientReasons = [],
        public ?string $failureReason = null,
    ) {}
}
