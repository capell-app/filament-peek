@props([
    'layout' => $widget->getMeta('layout', 'grid'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'widget',
])

<x-capell-layout-builder::widget.wrapper
    class="widget-ap-feature-list"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$widget
>
    <section class="ap-showcase-feature-list capell-showcase">
        <div class="capell-showcase__inner">
            @if ($widget->translation)
                <div class="capell-showcase__section-head">
                    @if ($widget->translation->title)
                        <h2
                            class="ap-feature-list-headline capell-showcase__heading"
                        >
                            {{ $widget->translation->title }}
                        </h2>
                    @endif

                    @if ($widget->translation->content)
                        <p
                            class="ap-feature-list-description capell-showcase__copy"
                        >
                            {!! strip_tags($widget->translation->content) !!}
                        </p>
                    @endif
                </div>
            @endif

            <div
                @class([
                    'ap-feature-list' => $layout === 'vertical',
                    'ap-feature-grid' => $layout !== 'vertical',
                ])
            >
                @forelse ($widget->assets as $widgetAsset)
                    @php
                        $asset = $widgetAsset->asset;
                        $icon = (string) $asset->getMeta('icon', '');
                    @endphp

                    <article class="ap-feature-item layout-builder-card">
                        @if ($icon !== '')
                            <span class="ap-feature-item__icon">
                                @if (str_starts_with($icon, 'heroicon-'))
                                    @svg($icon, 'h-5 w-5')
                                @else
                                    {{ $icon }}
                                @endif
                            </span>
                        @endif

                        @if ($asset->translation?->title)
                            <h3 class="ap-feature-title ap-feature-item__title">
                                {{ $asset->translation->title }}
                            </h3>
                        @endif

                        @if ($asset->translation?->content)
                            <p
                                class="ap-feature-description ap-feature-item__description"
                            >
                                {{ strip_tags($asset->translation->content) }}
                            </p>
                        @endif
                    </article>
                @empty
                    <div class="py-12 text-slate-300">
                        No features configured.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-capell-layout-builder::widget.wrapper>
