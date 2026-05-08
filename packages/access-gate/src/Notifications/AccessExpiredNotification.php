<?php

declare(strict_types=1);

namespace Capell\AccessGate\Notifications;

use Capell\AccessGate\Models\Area;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccessExpiredNotification extends Notification
{
    public function __construct(
        private readonly Area $area,
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
        return (new MailMessage)
            ->subject(__('capell-access-gate::notifications.expired.subject', ['area' => $this->area->name]))
            ->greeting(__('capell-access-gate::notifications.expired.greeting'))
            ->line(__('capell-access-gate::notifications.expired.lines.expired', ['area' => $this->area->name]));
    }
}
