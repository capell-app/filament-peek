<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Support\Providers;

use Capell\EmailStudio\Contracts\EmailProviderAdapter;
use Capell\EmailStudio\Data\InboundEmailReplyData;
use Capell\EmailStudio\Data\ProviderSendResultData;
use Capell\EmailStudio\Data\ProviderWebhookEventData;
use Capell\EmailStudio\Enums\EmailRecipientStatus;
use Capell\EmailStudio\Models\EmailMessage;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class SmtpEmailProviderAdapter implements EmailProviderAdapter
{
    public function send(EmailMessage $message): ProviderSendResultData
    {
        $message->loadMissing(['profile', 'recipients']);

        $deliverableRecipients = $message->recipients
            ->filter(function ($recipient): bool {
                $status = $recipient->status instanceof EmailRecipientStatus
                    ? $recipient->status
                    : EmailRecipientStatus::from((string) $recipient->status);

                return $status === EmailRecipientStatus::Queued;
            });

        $this->mailer($message)->send([], [], function (Message $mail) use ($message, $deliverableRecipients): void {
            $mail->subject($message->subject);
            $mail->from($message->profile->from_email, $message->profile->from_name);

            if ($message->profile->reply_to_email !== null) {
                $mail->replyTo($message->profile->reply_to_email, $message->profile->reply_to_name);
            }

            foreach ($deliverableRecipients as $recipient) {
                if ($recipient->type === 'cc') {
                    $mail->cc($recipient->email, $recipient->name);

                    continue;
                }

                if ($recipient->type === 'bcc') {
                    $mail->bcc($recipient->email, $recipient->name);

                    continue;
                }

                $mail->to($recipient->email, $recipient->name);
            }

            if ($message->rendered_html !== null) {
                $mail->html($message->rendered_html);
            }

            if ($message->rendered_text !== null) {
                $mail->text($message->rendered_text);
            }
        });

        return new ProviderSendResultData(
            successful: true,
            recipientProviderMessageIds: $deliverableRecipients
                ->mapWithKeys(fn ($recipient): array => [
                    (int) $recipient->getKey() => sprintf('smtp-%s-%s', $message->getKey(), $recipient->getKey()),
                ])
                ->all(),
        );
    }

    public function normalizeWebhookPayload(array $payload, array $headers = []): ProviderWebhookEventData
    {
        return new ProviderWebhookEventData(
            provider: 'smtp',
            eventType: (string) ($payload['event'] ?? 'sent'),
            providerMessageId: isset($payload['message_id']) ? (string) $payload['message_id'] : null,
            recipientEmail: isset($payload['recipient']) ? (string) $payload['recipient'] : null,
            idempotencyKey: isset($payload['id']) ? (string) $payload['id'] : null,
            payload: $payload,
        );
    }

    public function normalizeInboundReply(array $payload, array $headers = []): InboundEmailReplyData
    {
        return new InboundEmailReplyData(
            provider: 'smtp',
            providerMessageId: isset($payload['message_id']) ? (string) $payload['message_id'] : null,
            fromEmail: (string) ($payload['from_email'] ?? ''),
            fromName: isset($payload['from_name']) ? (string) $payload['from_name'] : null,
            subject: isset($payload['subject']) ? (string) $payload['subject'] : null,
            textBody: isset($payload['text']) ? (string) $payload['text'] : null,
            htmlBody: isset($payload['html']) ? (string) $payload['html'] : null,
            payload: $payload,
        );
    }

    protected function mailer(EmailMessage $message): MailerContract
    {
        $mailerName = $this->mailerName($message);

        return Mail::mailer($mailerName);
    }

    protected function mailerName(EmailMessage $message): ?string
    {
        $mailerName = $message->profile->provider_settings['mailer'] ?? null;

        return is_string($mailerName) ? $mailerName : null;
    }
}
