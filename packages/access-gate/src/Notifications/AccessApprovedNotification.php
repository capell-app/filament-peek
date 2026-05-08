<?php

declare(strict_types=1);

namespace Capell\AccessGate\Notifications;

use Capell\AccessGate\Models\Area;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccessApprovedNotification extends Notification
{
    public function __construct(
        private readonly Area $area,
        private readonly ?string $claimUrl = null,
    ) {}

    /**
     * @return list<string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('capell-access-gate::notifications.approved.subject', ['area' => $this->area->name]))
            ->greeting(__('capell-access-gate::notifications.approved.greeting'))
            ->line(__('capell-access-gate::notifications.approved.lines.approved', ['area' => $this->area->name]));

        if ($this->claimUrl !== null) {
            $message
                ->action(__('capell-access-gate::notifications.approved.action'), $this->claimUrl)
                ->line(__('capell-access-gate::notifications.approved.lines.claim'));
        }

        return $message;
    }
}
