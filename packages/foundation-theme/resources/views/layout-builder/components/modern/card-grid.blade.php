@props([
    'columns' => (int) ($widget->getMeta('columns', 3)),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'widget',
])

<x-capell-layout-builder::widget.wrapper
    class="widget-ap-card-grid"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$widget
>
    <section class="ap-showcase-card-grid capell-showcase">
        <div class="capell-showcase__inner">
            @if ($widget->translation)
                <div class="capell-showcase__section-head">
                    @if ($widget->translation->title)
                        <h2
                            class="ap-card-grid-headline capell-showcase__heading"
                        >
                            {{ $widget->translation->title }}
                        </h2>
                    @endif

                    @if ($widget->translation->content)
                        <p
                            class="ap-card-grid-description capell-showcase__copy"
                        >
                            {!! strip_tags($widget->translation->content) !!}
                        </p>
                    @endif
                </div>
            @endif

            <div
                class="ap-card-grid"
                style="--ap-card-columns: {{ max(1, min(4, $columns)) }}"
            >
                @if ($widget->assets->isNotEmpty())
                    @foreach ($widget->assets as $widgetAsset)
                        @php
                            $asset = $widgetAsset->asset;
                            $icon = (string) $asset->getMeta('icon', '');
                            $accent = $asset->getMeta('accent', 'teal');
                            $role = $asset->getMeta('role', 'card');
                            $caption = $asset->getMeta('caption');
                        @endphp

                        <article
                            class="ap-card layout-builder-card"
                            data-accent="{{ $accent }}"
                            data-role="{{ $role }}"
                        >
                            @if ($icon !== '')
                                <span class="ap-card__icon">
                                    @if (str_starts_with($icon, 'heroicon-'))
                                        @svg($icon, 'h-5 w-5')
                                    @else
                                        {{ $icon }}
                                    @endif
                                </span>
                            @endif

                            @if ($asset->translation?->title)
                                <h3 class="ap-card-title ap-card__title">
                                    {{ $caption ?: $asset->translation->title }}
                                </h3>
                            @endif

                            @if ($asset->translation?->content)
                                <p
                                    class="ap-card-description ap-card__description"
                                >
                                    {{ strip_tags($asset->translation->content) }}
                                </p>
                            @endif

                            @if ($asset->getMeta('link_text') && $asset->getMeta('link_url'))
                                <a
                                    href="{{ $asset->getMeta('link_url') }}"
                                    class="ap-card-link ap-card__link"
                                >
                                    <span>
                                        {{ $asset->getMeta('link_text') }}
                                    </span>
                                    @svg('heroicon-o-arrow-right', 'h-4 w-4')
                                </a>
                            @endif
                        </article>
                    @endforeach
                @elseif ($widget->getMeta('cards'))
                    @foreach ($widget->getMeta('cards') as $card)
                        <article class="ap-card layout-builder-card">
                            @if (! empty($card['icon']))
                                <span class="ap-card__icon">
                                    {{ $card['icon'] }}
                                </span>
                            @endif

                            @if (! empty($card['title']))
                                <h3 class="ap-card-title ap-card__title">
                                    {{ $card['title'] }}
                                </h3>
                            @endif

                            @if (! empty($card['description']))
                                <p
                                    class="ap-card-description ap-card__description"
                                >
                                    {{ $card['description'] }}
                                </p>
                            @endif
                        </article>
                    @endforeach
                @else
                    <div class="col-span-full py-12 text-center text-slate-500">
                        No cards configured.
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-capell-layout-builder::widget.wrapper>
