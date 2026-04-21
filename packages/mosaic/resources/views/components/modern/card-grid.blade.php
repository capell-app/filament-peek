<?php

declare(strict_types=1);

?>

@props([
    'columns' => (int) ($widget->getMeta('columns', 3)),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'widget',
])

<x-capell-mosaic::widget.wrapper
    class="widget-ap-card-grid"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$widget
>
    <section
        style="padding: 3rem 2rem; background-color: var(--mosaic-surface)"
    >
        @if ($widget->translation)
            <div style="margin-bottom: 2.5rem; max-width: 38rem">
                @if ($widget->translation->title)
                    <h2
                        class="ap-card-grid-headline"
                        style="
                            color: var(--mosaic-on-surface);
                            font-family: var(--mosaic-font-headline);
                            font-size: var(--mosaic-text-headline-lg);
                            font-weight: 700;
                            margin-bottom: 0.75rem;
                        "
                    >
                        {{ $widget->translation->title }}
                    </h2>
                @endif

                @if ($widget->translation->content)
                    <p
                        class="ap-card-grid-description"
                        style="
                            color: var(--mosaic-on-surface-variant);
                            font-size: var(--mosaic-text-body-lg);
                            line-height: 1.6;
                        "
                    >
                        {!! strip_tags($widget->translation->content) !!}
                    </p>
                @endif
            </div>
        @endif

        <div
            style="
                display: grid;
                grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr));
                gap: 1.5rem;
            "
        >
            @forelse ($widget->assets as $widgetAsset)
                <div
                    class="ap-card mosaic-card"
                    style="background-color: var(--mosaic-surface-container)"
                >
                    @if ($widgetAsset->asset->getMeta('icon'))
                        <div style="font-size: 2rem; margin-bottom: 1rem">
                            {{ $widgetAsset->asset->getMeta('icon') }}
                        </div>
                    @endif

                    @if ($widgetAsset->asset->translation?->title)
                        <h3
                            class="ap-card-title"
                            style="
                                color: var(--mosaic-on-surface);
                                font-size: var(--mosaic-text-title-lg);
                                font-weight: 600;
                                margin-bottom: 0.5rem;
                            "
                        >
                            {{ $widgetAsset->asset->translation->title }}
                        </h3>
                    @endif

                    @if ($widgetAsset->asset->translation?->content)
                        <p
                            class="ap-card-description"
                            style="
                                color: var(--mosaic-on-surface-variant);
                                font-size: var(--mosaic-text-body-md);
                                line-height: 1.55;
                            "
                        >
                            {{ strip_tags($widgetAsset->asset->translation->content) }}
                        </p>
                    @endif

                    @if ($widgetAsset->asset->getMeta('link_text') && $widgetAsset->asset->getMeta('link_url'))
                        <a
                            href="{{ $widgetAsset->asset->getMeta('link_url') }}"
                            class="ap-card-link"
                            style="
                                display: inline-flex;
                                align-items: center;
                                gap: 0.375rem;
                                margin-top: 1rem;
                                font-size: var(--mosaic-text-body-sm);
                                font-weight: 600;
                                color: var(--mosaic-primary);
                                text-decoration: none;
                            "
                        >
                            {{ $widgetAsset->asset->getMeta('link_text') }} →
                        </a>
                    @endif
                </div>
            @empty
                <div
                    style="
                        grid-column: 1 / -1;
                        text-align: center;
                        padding: 3rem;
                        color: var(--mosaic-on-surface-variant);
                    "
                >
                    No cards configured.
                </div>
            @endforelse
        </div>
    </section>
</x-capell-mosaic::widget.wrapper>

<?php
