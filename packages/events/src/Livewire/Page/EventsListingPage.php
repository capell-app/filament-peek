<?php

declare(strict_types=1);

namespace Capell\Events\Livewire\Page;

use Capell\Events\Actions\QueryPublicEventOccurrencesAction;
use Capell\Frontend\Facades\Frontend;
use Capell\Frontend\Livewire\Page\AbstractPage;
use Carbon\CarbonImmutable;

class EventsListingPage extends AbstractPage
{
    protected static string $defaultView = 'capell-events::livewire.page.events-listing';

    protected function setup(): void
    {
        $now = CarbonImmutable::now();

        $this->results = QueryPublicEventOccurrencesAction::run(
            Frontend::site(),
            $now->subDay(),
            $now->addYear(),
            (int) (Frontend::page()->meta['limit'] ?? config('capell-frontend.pagination_limit', 12)),
        );
    }
}
