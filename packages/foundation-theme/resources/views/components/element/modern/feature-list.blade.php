@props([
    'layout' => $element->getMeta('layout', 'grid'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'element',
])

<x-capell-foundation-theme::element.wrapper
    class="capell-modern-feature-list element-ap-feature-list"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <section class="ap-showcase-feature-list capell-showcase">
        <div class="capell-showcase__inner">
            @if ($element->translation)
                <div class="capell-showcase__section-head">
                    @if ($element->translation->title)
                        <h2
                            class="ap-feature-list-headline capell-showcase__heading"
                        >
                            {{ $element->translation->title }}
                        </h2>
                    @endif

                    @if ($element->translation->content)
                        <p
                            class="ap-feature-list-description capell-showcase__copy"
                        >
                            {!! strip_tags($element->translation->content) !!}
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
                @forelse ($element->assets as $elementAsset)
                    @php
                        $asset = $elementAsset->asset;
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
</x-capell-foundation-theme::element.wrapper>
