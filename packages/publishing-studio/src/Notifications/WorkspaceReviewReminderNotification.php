<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Notifications;

use Capell\PublishingStudio\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class WorkspaceReviewReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Workspace $workspace) {}

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
            ->subject((string) __('capell-publishing-studio::scheduler.notifications.review_reminder_subject', [
                'workspace' => $this->workspace->name,
            ]))
            ->line((string) __('capell-publishing-studio::scheduler.notifications.review_reminder_body', [
                'workspace' => $this->workspace->name,
            ]));
    }
}
