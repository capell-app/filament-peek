<?php

declare(strict_types=1);

namespace Capell\Events\Livewire\Page;

use Capell\Frontend\Livewire\Page\AbstractPage;

class EventsCalendarPage extends AbstractPage
{
    protected static string $defaultView = 'capell-events::livewire.page.events-calendar';

    protected function setup(): void
    {
        $this->params = [];
    }
}
