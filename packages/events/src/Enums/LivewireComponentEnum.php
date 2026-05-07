<?php

declare(strict_types=1);

namespace Capell\Events\Enums;

use Capell\Events\Livewire\EventCalendar;
use Capell\Events\Livewire\Page\EventsCalendarPage;
use Capell\Events\Livewire\Page\EventsListingPage;

enum LivewireComponentEnum: string
{
    case EventCalendar = 'capell-events::event-calendar';
    case EventsCalendarPage = 'capell-events::page.events-calendar';
    case EventsListingPage = 'capell-events::page.events-listing';

    /**
     * @return array<string, class-string>
     */
    public static function getComponents(): array
    {
        return [
            self::EventCalendar->value => EventCalendar::class,
            self::EventsCalendarPage->value => EventsCalendarPage::class,
            self::EventsListingPage->value => EventsListingPage::class,
        ];
    }
}
