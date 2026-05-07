<?php

declare(strict_types=1);

namespace Capell\Events\Livewire;

use Capell\Core\Models\Site;
use Capell\Events\Actions\QueryPublicEventOccurrencesAction;
use Capell\Events\Models\EventOccurrence;
use Capell\Events\Support\Calendar\CalendarMonth;
use Capell\Frontend\Facades\Frontend;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Livewire\Component;

class EventCalendar extends Component
{
    public string $month;

    public function mount(?string $month = null): void
    {
        $this->month = $month ?? CarbonImmutable::now($this->site()->timezone ?? 'UTC')->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->month = $this->calendarMonth()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->calendarMonth()->addMonth()->format('Y-m');
    }

    public function render(): mixed
    {
        $month = $this->calendarMonth();
        $occurrences = QueryPublicEventOccurrencesAction::run($this->site(), $month->startOfMonth()->startOfWeek(), $month->endOfMonth()->endOfWeek());

        return view('capell-events::livewire.event-calendar', [
            'monthDate' => $month,
            'weeks' => resolve(CalendarMonth::class)->weeks($month),
            'occurrencesByDate' => $this->occurrencesByDate($occurrences),
        ]);
    }

    private function site(): Site
    {
        return Frontend::site();
    }

    private function calendarMonth(): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('Y-m-d', $this->month . '-01') ?? CarbonImmutable::now();
    }

    /**
     * @param  Collection<int, EventOccurrence>  $occurrences
     * @return array<string, Collection<int, EventOccurrence>>
     */
    private function occurrencesByDate(Collection $occurrences): array
    {
        return $occurrences
            ->groupBy(fn (EventOccurrence $occurrence): string => $occurrence->starts_at->setTimezone($occurrence->timezone)->toDateString())
            ->all();
    }
}
