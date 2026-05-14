@props([
    'title' => $widget->translation?->title,
    'content' => $widget->translation?->content,
    'columns' => (int) ($widget->getMeta('columns', 3)),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'widget',
])

<x-capell-layout-builder::widget.wrapper
    class="widget-ap-image-gallery"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$widget
>
    <section class="ap-showcase-gallery capell-showcase">
        <div class="capell-showcase__inner">
            @if ($title || $content)
                <div class="capell-showcase__section-head">
                    @if ($title)
                        <h2
                            class="ap-gallery-headline capell-showcase__heading"
                        >
                            {{ $title }}
                        </h2>
                    @endif

                    @if ($content)
                        <p class="ap-gallery-description capell-showcase__copy">
                            {!! strip_tags($content) !!}
                        </p>
                    @endif
                </div>
            @endif

            @if ($widget->assets->isNotEmpty())
                <div
                    class="ap-gallery-grid"
                    style="
                        --ap-gallery-columns: {{ max(1, min(4, $columns)) }};
                    "
                >
                    @foreach ($widget->assets as $asset)
                        @php
                            $media =
                                $asset->media->firstWhere(
                                    'collection_name',
                                    'image',
                                ) ?:
                                $asset->asset->media->firstWhere(
                                    'collection_name',
                                    'image',
                                );
                            $caption = $asset->asset->translation?->title ?? $media?->name;
                        @endphp

                        @if ($media)
                            <figure class="ap-gallery-item">
                                <img
                                    src="{{ $media->getFullUrl() }}"
                                    alt="{{ $caption }}"
                                />
                                <figcaption class="ap-gallery-caption">
                                    <span>{{ $caption }}</span>
                                    @svg('heroicon-o-arrows-pointing-out', 'h-4 w-4 text-slate-400')
                                </figcaption>
                            </figure>
                        @endif
                    @endforeach
                </div>
            @elseif ($widget->image)
                <div
                    class="ap-gallery-grid"
                    style="
                        --ap-gallery-columns: {{ max(1, min(4, $columns)) }};
                    "
                >
                    <figure class="ap-gallery-item">
                        <img
                            src="{{ $widget->image->getFullUrl() }}"
                            alt="{{ $widget->image->name }}"
                        />
                        <figcaption class="ap-gallery-caption">
                            <span>{{ $widget->image->name }}</span>
                            @svg('heroicon-o-arrows-pointing-out', 'h-4 w-4 text-slate-400')
                        </figcaption>
                    </figure>
                </div>
            @else
                <div class="py-12 text-center text-slate-500">
                    No images configured.
                </div>
            @endif
        </div>
    </section>
</x-capell-layout-builder::widget.wrapper>
