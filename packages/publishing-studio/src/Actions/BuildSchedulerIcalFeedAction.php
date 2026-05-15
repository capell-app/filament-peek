<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Actions\DashboardReports\BuildContentSchedulerEventsAction;
use Capell\PublishingStudio\Enums\SchedulerIcalFeedScopeEnum;
use Capell\PublishingStudio\Models\SchedulerIcalToken;
use Carbon\CarbonImmutable;
use Lorisleiva\Actions\Concerns\AsAction;

final class BuildSchedulerIcalFeedAction
{
    use AsAction;

    public function handle(SchedulerIcalToken $token): string
    {
        $startsAt = CarbonImmutable::now()->subMonth();
        $endsAt = CarbonImmutable::now()->addMonths(6);

        $events = BuildContentSchedulerEventsAction::run(
            startsAt: $startsAt,
            endsAt: $endsAt,
            siteId: $token->scope === SchedulerIcalFeedScopeEnum::Site ? $token->site_id : null,
            ownerId: $token->scope === SchedulerIcalFeedScopeEnum::Mine ? $token->owner_id : null,
            ownerType: $token->scope === SchedulerIcalFeedScopeEnum::Mine ? $token->owner_type : null,
            limit: 500,
        );

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Capell//Publishing Studio Scheduler//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($events as $event) {
            $uid = hash('sha256', implode(':', [$event->id, $event->scheduledFor->getTimestamp()])) . '@capell';
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:' . $uid;
            $lines[] = 'DTSTAMP:' . CarbonImmutable::instance($event->scheduledFor)->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTSTART:' . CarbonImmutable::instance($event->scheduledFor)->utc()->format('Ymd\THis\Z');
            $lines[] = 'SUMMARY:' . $this->escape($event->eventType->getLabel() . ': ' . $event->title);
            $lines[] = 'DESCRIPTION:' . $this->escape((string) $event->description);
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    private function escape(string $value): string
    {
        return str_replace(['\\', "\n", "\r", ',', ';'], ['\\\\', '\n', '', '\,', '\;'], $value);
    }
}
