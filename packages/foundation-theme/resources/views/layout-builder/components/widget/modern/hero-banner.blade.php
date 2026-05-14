@props([
    'title' => $widget->translation?->title,
    'content' => $widget->translation?->content,
    'primaryButtonText' => $widget->getMeta('primary_button_text'),
    'primaryButtonUrl' => $widget->getMeta('primary_button_url', '#'),
    'secondaryButtonText' => $widget->getMeta('secondary_button_text'),
    'secondaryButtonUrl' => $widget->getMeta('secondary_button_url', '#'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'widget',
])

@php
    $backgroundImage = $widget->backgroundImage ?? $widget->image;
    $backgroundImageUrl = $backgroundImage?->getAvailableFullUrl(['large']);
@endphp

<x-capell-layout-builder::widget.wrapper
    class="widget-ap-hero-banner"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$widget
>
    <section
        class="ap-hero capell-showcase relative overflow-hidden"
        @if ($backgroundImageUrl)
            style="background-image: url('{{ $backgroundImageUrl }}'); background-size: cover; background-position: center;"
        @endif
    >
        <div class="ap-hero__overlay"></div>

        <div class="ap-hero__inner capell-showcase__inner">
            <div class="ap-hero__content">
                @if ($title)
                    <h1
                        class="ap-hero__title capell-showcase__heading ap-headline"
                    >
                        {{ $title }}
                    </h1>
                @endif

                @if ($content)
                    <p class="ap-hero__copy capell-showcase__copy">
                        {!! strip_tags($content) !!}
                    </p>
                @endif

                @if ($primaryButtonText || $secondaryButtonText)
                    <div class="ap-hero__actions">
                        @if ($primaryButtonText)
                            <a
                                href="{{ $primaryButtonUrl }}"
                                class="ap-hero__button ap-hero__button--primary ap-cta-primary"
                            >
                                <span>{{ $primaryButtonText }}</span>
                                @svg('heroicon-o-arrow-right', 'h-4 w-4')
                            </a>
                        @endif

                        @if ($secondaryButtonText)
                            <a
                                href="{{ $secondaryButtonUrl }}"
                                class="ap-hero__button ap-hero__button--secondary ap-cta-secondary"
                            >
                                <span>{{ $secondaryButtonText }}</span>
                                @svg('heroicon-o-code-bracket', 'h-4 w-4')
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <div class="ap-hero__product" aria-hidden="true">
                <div class="ap-hero__panel">
                    <div class="ap-hero__panel-header">
                        <span class="ap-hero__panel-title">
                            Capell control plane
                        </span>
                        <span class="ap-hero__panel-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </div>

                    <div class="ap-hero__panel-body">
                        <div class="ap-hero__rail">
                            <span class="ap-hero__rail-icon">
                                @svg('heroicon-o-circle-stack', 'h-5 w-5')
                            </span>
                            <span>
                                <span class="ap-hero__rail-title">
                                    Content model
                                </span>
                                <span class="ap-hero__rail-copy">
                                    Pages, sections, widgets, media
                                </span>
                            </span>
                            <span
                                class="ap-hero__rail-status ap-hero__rail-status--blue"
                            >
                                Typed
                            </span>
                        </div>

                        <div class="ap-hero__rail">
                            <span class="ap-hero__rail-icon">
                                @svg('heroicon-o-rectangle-group', 'h-5 w-5')
                            </span>
                            <span>
                                <span class="ap-hero__rail-title">
                                    Layout builder
                                </span>
                                <span class="ap-hero__rail-copy">
                                    Composable editor-owned frontend
                                </span>
                            </span>
                            <span
                                class="ap-hero__rail-status ap-hero__rail-status--green"
                            >
                                Live
                            </span>
                        </div>

                        <div class="ap-hero__rail">
                            <span class="ap-hero__rail-icon">
                                @svg('heroicon-o-bolt', 'h-5 w-5')
                            </span>
                            <span>
                                <span class="ap-hero__rail-title">
                                    Static delivery
                                </span>
                                <span class="ap-hero__rail-copy">
                                    Generated HTML, warm cache, fast routes
                                </span>
                            </span>
                            <span
                                class="ap-hero__rail-status ap-hero__rail-status--blue"
                            >
                                Ready
                            </span>
                        </div>

                        <div class="ap-hero__rail">
                            <span class="ap-hero__rail-icon">
                                @svg('heroicon-o-puzzle-piece', 'h-5 w-5')
                            </span>
                            <span>
                                <span class="ap-hero__rail-title">
                                    Package runtime
                                </span>
                                <span class="ap-hero__rail-copy">
                                    Frontend assets owned by each package
                                </span>
                            </span>
                            <span
                                class="ap-hero__rail-status ap-hero__rail-status--green"
                            >
                                Verified
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-capell-layout-builder::widget.wrapper>
