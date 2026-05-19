@props([
    'columns' => $block->getMeta('columns', 2),
    'displayMode' => $block->getMeta('display_mode', 'grid'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'block',
])

@php
    $gridClasses = [
        1 => 'mx-auto max-w-2xl grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
    ];

    $gridClass = $gridClasses[(int) $columns] ?? $gridClasses[2];
    $responsiveGrid = '!flex snap-x gap-4 !overflow-x-auto pb-3 [scrollbar-width:none] md:!grid md:!overflow-visible md:pb-0 [&::-webkit-scrollbar]:hidden';
    $assets = $block->assets;
@endphp

<x-capell-foundation-theme::block.wrapper
    class="capell-modern-testimonials block-ap-testimonials"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$block
>
    <section class="px-6 py-12 md:px-12 md:py-16">
        @if ($block->translation)
            <div class="mx-auto mb-12 max-w-2xl text-center">
                @if ($block->translation->title)
                    <h2
                        class="mb-4 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl"
                    >
                        {{ $block->translation->title }}
                    </h2>
                @endif

                @if ($block->translation->content)
                    <p class="text-lg text-gray-500">
                        {{ strip_tags($block->translation->content) }}
                    </p>
                @endif
            </div>
        @endif

        @if ($displayMode === 'carousel')
            <div
                class="layout-builder-testimonials-carousel relative mx-auto max-w-2xl"
            >
                <div class="relative overflow-hidden rounded-2xl">
                    <div
                        class="carousel-container flex transition-transform duration-300 ease-in-out"
                    >
                        @forelse ($assets as $blockAsset)
                            @php
                                $icon = $blockAsset->asset->getMeta('icon');
                                $role = $blockAsset->asset->getMeta('position');
                            @endphp

                            <div class="carousel-slide min-w-full">
                                <div
                                    class="h-full rounded-xl border border-stone-200 bg-white p-8"
                                >
                                    <div
                                        class="mb-4 font-serif text-5xl leading-none text-stone-300"
                                    >
                                        &ldquo;
                                    </div>

                                    @if ($blockAsset->asset->translation?->content)
                                        <blockquote class="mb-6">
                                            <p
                                                class="text-lg italic leading-relaxed text-gray-700"
                                            >
                                                {{ strip_tags($blockAsset->asset->translation->content) }}
                                            </p>
                                        </blockquote>
                                    @endif

                                    <div
                                        class="flex items-center gap-4 border-t border-gray-200 pt-6"
                                    >
                                        @if ($icon)
                                            <div
                                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 text-blue-700"
                                            >
                                                @if (str_starts_with((string) $icon, 'heroicon-'))
                                                    @svg($icon, 'h-5 w-5')
                                                @else
                                                    <span class="text-3xl">
                                                        {{ $icon }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        <div>
                                            @if ($blockAsset->asset->translation?->title)
                                                <p
                                                    class="font-bold text-gray-900"
                                                >
                                                    {{ $blockAsset->asset->translation->title }}
                                                </p>
                                            @endif

                                            @if ($role)
                                                <p
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{ $role }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="w-full py-12 text-center">
                                <p class="text-gray-500">
                                    No testimonials configured
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($assets->count() > 1)
                    <button
                        class="carousel-prev absolute left-0 top-1/2 -translate-x-12 -translate-y-1/2 text-2xl text-gray-600 hover:text-gray-900"
                        onclick="slideCarousel(this, -1)"
                    >
                        ←
                    </button>
                    <button
                        class="carousel-next absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 text-2xl text-gray-600 hover:text-gray-900"
                        onclick="slideCarousel(this, 1)"
                    >
                        →
                    </button>

                    <div class="mt-6 flex justify-center gap-2">
                        @for ($dotIndex = 0; $dotIndex < $assets->count(); $dotIndex++)
                            <button
                                class="carousel-dot h-2.5 w-2.5 rounded-full transition-all"
                                style="
                                    background-color: {{ $dotIndex === 0 ? '#4f46e5' : '#d1d5db' }};
                                "
                                onclick="goToSlide(this, {{ $dotIndex }})"
                            ></button>
                        @endfor
                    </div>
                @endif
            </div>
        @else
            <div class="{{ $responsiveGrid }} {{ $gridClass }}">
                @forelse ($assets as $blockAsset)
                    @php
                        $icon = $blockAsset->asset->getMeta('icon');
                        $role = $blockAsset->asset->getMeta('position');
                    @endphp

                    <div
                        class="min-w-full snap-start rounded-xl border border-stone-200 bg-white p-6 md:min-w-0 md:p-8"
                    >
                        <div
                            class="mb-4 font-serif text-5xl leading-none text-indigo-200"
                        >
                            &ldquo;
                        </div>

                        @if ($blockAsset->asset->translation?->content)
                            <blockquote class="mb-6">
                                <p
                                    class="text-lg italic leading-relaxed text-gray-700"
                                >
                                    {{ strip_tags($blockAsset->asset->translation->content) }}
                                </p>
                            </blockquote>
                        @endif

                        <div
                            class="flex items-center gap-4 border-t border-gray-200 pt-6"
                        >
                            @if ($icon)
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 text-blue-700"
                                >
                                    @if (str_starts_with((string) $icon, 'heroicon-'))
                                        @svg($icon, 'h-5 w-5')
                                    @else
                                        <span class="text-3xl">
                                            {{ $icon }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div>
                                @if ($blockAsset->asset->translation?->title)
                                    <p class="font-bold text-gray-900">
                                        {{ $blockAsset->asset->translation->title }}
                                    </p>
                                @endif

                                @if ($role)
                                    <p class="text-sm text-gray-500">
                                        {{ $role }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <p class="text-gray-500">No testimonials configured</p>
                    </div>
                @endforelse
            </div>
        @endif
    </section>
</x-capell-foundation-theme::block.wrapper>

<script>
    function slideCarousel(button, direction) {
        const carousel = button.closest('.layout-builder-testimonials-carousel')
        const container = carousel.querySelector('.carousel-container')
        const slides = carousel.querySelectorAll('.carousel-slide')
        const currentOffset =
            parseInt(
                container.style.transform?.replace('translateX(', '') ?? '0',
            ) || 0
        const currentIndex = Math.round(-currentOffset / 100)

        let newIndex = currentIndex + direction
        if (newIndex < 0) newIndex = slides.length - 1
        if (newIndex >= slides.length) newIndex = 0

        container.style.transform = `translateX(${-newIndex * 100}%)`
        updateDots(carousel, newIndex)
    }

    function goToSlide(dotButton, index) {
        const carousel = dotButton.closest(
            '.layout-builder-testimonials-carousel',
        )
        const container = carousel.querySelector('.carousel-container')
        container.style.transform = `translateX(${-index * 100}%)`
        updateDots(carousel, index)
    }

    function updateDots(carousel, activeIndex) {
        carousel.querySelectorAll('.carousel-dot').forEach((dot, index) => {
            dot.style.backgroundColor =
                index === activeIndex ? '#1c1917' : '#d6d3d1'
        })
    }
</script>
