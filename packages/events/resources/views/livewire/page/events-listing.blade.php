<div class="capell-page-events-listing">
    @foreach ($results ?? [] as $occurrence)
        <article>
            <h2>
                <a href="{{ $occurrence->occurrenceUrl() }}">
                    {{ $occurrence->event->translation?->title ?? $occurrence->event->name }}
                </a>
            </h2>
            <time datetime="{{ $occurrence->starts_at->toIso8601String() }}">
                {{ $occurrence->starts_at->setTimezone($occurrence->timezone)->format('j F Y H:i') }}
            </time>
        </article>
    @endforeach
</div>
