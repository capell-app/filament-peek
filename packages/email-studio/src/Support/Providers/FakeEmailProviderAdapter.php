<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Support\Providers;

use Capell\EmailStudio\Contracts\EmailProviderAdapter;
use Capell\EmailStudio\Data\InboundEmailReplyData;
use Capell\EmailStudio\Data\ProviderSendResultData;
use Capell\EmailStudio\Data\ProviderWebhookEventData;
use Capell\EmailStudio\Enums\EmailRecipientStatus;
use Capell\EmailStudio\Models\EmailMessage;

class FakeEmailProviderAdapter implements EmailProviderAdapter
{
    public function send(EmailMessage $message): ProviderSendResultData
    {
        $message->loadMissing('recipients');

        $providerMessageIds = [];

        foreach ($message->recipients as $recipient) {
            $status = $recipient->status instanceof EmailRecipientStatus
                ? $recipient->status
                : EmailRecipientStatus::from((string) $recipient->status);

            if ($status !== EmailRecipientStatus::Queued) {
                continue;
            }

            $providerMessageIds[(int) $recipient->getKey()] = sprintf(
                'fake-%s-%s',
                $message->getKey(),
                $recipient->getKey(),
            );
        }

        return new ProviderSendResultData(
            successful: true,
            recipientProviderMessageIds: $providerMessageIds,
        );
    }

    public function normalizeWebhookPayload(array $payload, array $headers = []): ProviderWebhookEventData
    {
        return new ProviderWebhookEventData(
            provider: 'fake',
            eventType: (string) ($payload['event'] ?? 'delivered'),
            providerMessageId: isset($payload['message_id']) ? (string) $payload['message_id'] : null,
            recipientEmail: isset($payload['recipient']) ? (string) $payload['recipient'] : null,
            idempotencyKey: isset($payload['id']) ? (string) $payload['id'] : null,
            payload: $payload,
        );
    }

    public function normalizeInboundReply(array $payload, array $headers = []): InboundEmailReplyData
    {
        return new InboundEmailReplyData(
            provider: 'fake',
            providerMessageId: isset($payload['message_id']) ? (string) $payload['message_id'] : null,
            fromEmail: (string) ($payload['from_email'] ?? ''),
            fromName: isset($payload['from_name']) ? (string) $payload['from_name'] : null,
            subject: isset($payload['subject']) ? (string) $payload['subject'] : null,
            textBody: isset($payload['text']) ? (string) $payload['text'] : null,
            htmlBody: isset($payload['html']) ? (string) $payload['html'] : null,
            payload: $payload,
        );
    }
}
