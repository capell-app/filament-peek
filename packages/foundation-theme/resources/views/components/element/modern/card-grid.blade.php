@props([
    'columns' => (int) ($element->getMeta('columns', 3)),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'element',
])

@php
    use Capell\FoundationTheme\Actions\BuildElementAssetRenderDataAction;
@endphp

<x-capell-foundation-theme::element.wrapper
    class="element-ap-card-grid"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <section class="ap-showcase-card-grid capell-showcase">
        <div class="capell-showcase__inner">
            @if ($element->translation)
                <div class="capell-showcase__section-head">
                    @if ($element->translation->title)
                        <h2
                            class="ap-card-grid-headline capell-showcase__heading"
                        >
                            {{ $element->translation->title }}
                        </h2>
                    @endif

                    @if ($element->translation->content)
                        <p
                            class="ap-card-grid-description capell-showcase__copy"
                        >
                            {!! strip_tags($element->translation->content) !!}
                        </p>
                    @endif
                </div>
            @endif

            <div
                class="ap-card-grid"
                style="--ap-card-columns: {{ max(1, min(4, $columns)) }}"
            >
                @if ($element->assets->isNotEmpty())
                    @foreach ($element->assets as $elementAsset)
                        @php
                            $assetRenderData = BuildElementAssetRenderDataAction::run($elementAsset);
                            $icon = $assetRenderData->icon ?? '';
                            $accent = $assetRenderData->accent ?? 'teal';
                            $role = $assetRenderData->role ?? 'card';
                            $cardTitle = $assetRenderData->caption ?? $assetRenderData->title;
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

                            @if ($cardTitle)
                                <h3 class="ap-card-title ap-card__title">
                                    {{ $cardTitle }}
                                </h3>
                            @endif

                            @if ($assetRenderData->content)
                                <p
                                    class="ap-card-description ap-card__description"
                                >
                                    {{ strip_tags($assetRenderData->content) }}
                                </p>
                            @endif

                            @if (($assetRenderData->meta['link_text'] ?? null) && ($assetRenderData->meta['link_url'] ?? null))
                                <a
                                    href="{{ $assetRenderData->meta['link_url'] }}"
                                    class="ap-card-link ap-card__link"
                                >
                                    <span>
                                        {{ $assetRenderData->meta['link_text'] }}
                                    </span>
                                    @svg('heroicon-o-arrow-right', 'h-4 w-4')
                                </a>
                            @endif
                        </article>
                    @endforeach
                @elseif ($element->getMeta('cards'))
                    @foreach ($element->getMeta('cards') as $card)
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
                @endif
            </div>
        </div>
    </section>
</x-capell-foundation-theme::element.wrapper>
