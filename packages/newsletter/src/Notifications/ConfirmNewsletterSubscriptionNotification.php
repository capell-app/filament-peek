<?php

declare(strict_types=1);

namespace Capell\Newsletter\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmNewsletterSubscriptionNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $token,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('capell-newsletter::messages.confirmation_subject'))
            ->line(__('capell-newsletter::messages.confirmation_line'))
            ->action(__('capell-newsletter::messages.confirmation_action'), route('capell-newsletter.confirm', [
                'token' => $this->token,
            ]));
    }
}
