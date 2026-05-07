<section class="capell-events-calendar">
    <div class="flex items-center justify-between gap-4">
        <button type="button" wire:click="previousMonth">
            {{ __('capell-events::generic.previous') }}
        </button>
        <h2>{{ $monthDate->format('F Y') }}</h2>
        <button type="button" wire:click="nextMonth">
            {{ __('capell-events::generic.next') }}
        </button>
    </div>

    <div class="grid grid-cols-7 gap-2">
        @foreach ($weeks as $week)
            @foreach ($week->days as $day)
                <div class="min-h-24 border p-2">
                    <div>{{ $day->day }}</div>

                    @foreach (($occurrencesByDate[$day->toDateString()] ?? collect()) as $occurrence)
                        <a
                            href="{{ $occurrence->occurrenceUrl() }}"
                            class="block"
                        >
                            {{ $occurrence->event->translation?->title ?? $occurrence->event->name }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        @endforeach
    </div>
</section>
