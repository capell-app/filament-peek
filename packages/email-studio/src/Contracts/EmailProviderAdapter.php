<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Contracts;

use Capell\EmailStudio\Data\InboundEmailReplyData;
use Capell\EmailStudio\Data\ProviderSendResultData;
use Capell\EmailStudio\Data\ProviderWebhookEventData;
use Capell\EmailStudio\Models\EmailMessage;

interface EmailProviderAdapter
{
    public function send(EmailMessage $message): ProviderSendResultData;

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    public function normalizeWebhookPayload(array $payload, array $headers = []): ProviderWebhookEventData;

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    public function normalizeInboundReply(array $payload, array $headers = []): InboundEmailReplyData;
}
