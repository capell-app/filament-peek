{{--
    Modern Testimonials Widget
    
    Props:
    - title (string): Section heading
    - testimonials (array): Array of testimonial objects
    - columns (int): Number of columns (1,2,3) - Default: 2
    - displayMode (string): 'grid|carousel' - Default: 'grid'
    - customizable (bool): Show admin hints
--}}

@props([
    'title' => 'What Customers Say',
    'testimonials' => [
        [
            'quote' => 'Amazing experience! Capell made it so easy to manage our content.',
            'author' => 'Sarah Johnson',
            'role' => 'Marketing Manager',
            'avatar' => '👩‍💼',
        ],
        [
            'quote' => 'Switched from other CMS platforms. Best decision ever!',
            'author' => 'Mike Chen',
            'role' => 'CEO',
            'avatar' => '👨‍💼',
        ],
    ],
    'columns' => 2,
    'displayMode' => 'grid',
    'customizable' => true,
])

@php
    $gridClasses = [
        1 => 'mx-auto max-w-2xl grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
    ];

    $gridClass = $gridClasses[$columns] ?? $gridClasses[2];
@endphp

<section class="mosaic-testimonials px-6 py-12 md:px-12 md:py-16">
    {{-- Header --}}
    @if ($title)
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <h2
                class="mb-4 text-3xl font-bold md:text-4xl"
                style="
                    color: var(--mosaic-on-surface);
                    font-family: var(--mosaic-font-headline);
                "
            >
                {{ $title }}
            </h2>
        </div>
    @endif

    @if ($displayMode === 'carousel')
        {{-- Carousel Mode --}}
        <div class="mosaic-testimonials-carousel relative mx-auto max-w-2xl">
            <div class="relative overflow-hidden rounded-lg">
                <div
                    class="carousel-container"
                    style="display: flex; transition: transform 0.3s ease"
                >
                    @forelse ($testimonials as $index => $testimonial)
                        <div
                            class="carousel-slide"
                            style="
                                min-width: 100%;
                                display: flex;
                                flex-direction: column;
                            "
                        >
                            <div
                                class="mosaic-card h-full"
                                style="
                                    background-color: var(
                                        --mosaic-surface-container
                                    );
                                "
                            >
                                {{-- Quote Mark --}}
                                <div
                                    class="mb-4 text-4xl"
                                    style="
                                        color: var(--mosaic-tertiary);
                                        opacity: 0.3;
                                    "
                                >
                                    "
                                </div>

                                {{-- Quote --}}
                                <blockquote class="mb-6">
                                    <p
                                        class="text-lg italic leading-relaxed"
                                        style="color: var(--mosaic-on-surface)"
                                    >
                                        {{ $testimonial['quote'] }}
                                    </p>
                                </blockquote>

                                {{-- Author Info --}}
                                <div
                                    class="mt-auto flex items-center gap-4 pt-6"
                                    style="
                                        border-top: 1px solid
                                            var(--mosaic-outline-variant);
                                    "
                                >
                                    {{-- Avatar --}}
                                    @if (isset($testimonial['avatar']))
                                        <div class="text-3xl">
                                            {{ $testimonial['avatar'] }}
                                        </div>
                                    @endif

                                    <div>
                                        @if (isset($testimonial['author']))
                                            <p
                                                class="text-base font-bold"
                                                style="
                                                    color: var(
                                                        --mosaic-on-surface
                                                    );
                                                "
                                            >
                                                {{ $testimonial['author'] }}
                                            </p>
                                        @endif

                                        @if (isset($testimonial['role']))
                                            <p
                                                class="text-sm"
                                                style="
                                                    color: var(
                                                        --mosaic-on-surface-variant
                                                    );
                                                "
                                            >
                                                {{ $testimonial['role'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="w-full py-12 text-center">
                            <p style="color: var(--mosaic-on-surface-variant)">
                                No testimonials configured
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Navigation Buttons --}}
            @if (count($testimonials) > 1)
                <button
                    class="carousel-prev absolute left-0 top-1/2 -translate-x-16 -translate-y-1/2 text-2xl"
                    style="color: var(--mosaic-on-surface); cursor: pointer"
                    onclick="slideCarousel(this, -1)"
                >
                    ←
                </button>
                <button
                    class="carousel-next absolute right-0 top-1/2 -translate-y-1/2 translate-x-16 text-2xl"
                    style="color: var(--mosaic-on-surface); cursor: pointer"
                    onclick="slideCarousel(this, 1)"
                >
                    →
                </button>

                {{-- Indicator Dots --}}
                <div class="mt-6 flex justify-center gap-2">
                    @for ($index = 0; $index < count($testimonials); $index++)
                        <button
                            class="carousel-dot h-3 w-3 rounded-full transition-all"
                            style="
                                background-color: {{ $index === 0 ? 'var(--mosaic-primary)' : 'var(--mosaic-outline)' }};
                                cursor: pointer;
                            "
                            onclick="goToSlide(this, {{ $index }})"
                        ></button>
                    @endfor
                </div>
            @endif
        </div>
    @else
        {{-- Grid Mode --}}
        <div class="{{ $gridClass }} grid gap-6">
            @forelse ($testimonials as $testimonial)
                <div
                    class="mosaic-card"
                    style="background-color: var(--mosaic-surface-container)"
                >
                    {{-- Quote Mark --}}
                    <div
                        class="mb-4 text-4xl"
                        style="color: var(--mosaic-tertiary); opacity: 0.3"
                    >
                        "
                    </div>

                    {{-- Quote --}}
                    <blockquote class="mb-6">
                        <p
                            class="text-lg italic leading-relaxed"
                            style="color: var(--mosaic-on-surface)"
                        >
                            {{ $testimonial['quote'] }}
                        </p>
                    </blockquote>

                    {{-- Author Info --}}
                    <div
                        class="flex items-center gap-4 pt-6"
                        style="
                            border-top: 1px solid var(--mosaic-outline-variant);
                        "
                    >
                        {{-- Avatar --}}
                        @if (isset($testimonial['avatar']))
                            <div class="text-3xl">
                                {{ $testimonial['avatar'] }}
                            </div>
                        @endif

                        <div>
                            @if (isset($testimonial['author']))
                                <p
                                    class="text-base font-bold"
                                    style="color: var(--mosaic-on-surface)"
                                >
                                    {{ $testimonial['author'] }}
                                </p>
                            @endif

                            @if (isset($testimonial['role']))
                                <p
                                    class="text-sm"
                                    style="
                                        color: var(--mosaic-on-surface-variant);
                                    "
                                >
                                    {{ $testimonial['role'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <p style="color: var(--mosaic-on-surface-variant)">
                        No testimonials configured
                    </p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Admin Hint --}}
    @if ($customizable && auth()->check())
        <div
            class="mt-12 max-w-full pt-8 text-center"
            style="
                border-top: 1px solid var(--mosaic-outline-variant);
                opacity: 0.6;
            "
        >
            <span class="mosaic-text-label text-xs">
                ✨ Customize: Add testimonials, layout mode (grid/carousel),
                columns
            </span>
        </div>
    @endif
</section>

<script>
    function slideCarousel(button, direction) {
        const carousel = button.closest('.mosaic-testimonials-carousel')
        const container = carousel.querySelector('.carousel-container')
        const slides = carousel.querySelectorAll('.carousel-slide')
        let currentIndex = 0

        slides.forEach((slide, index) => {
            if (slide.style.display !== 'none') {
                currentIndex = index
            }
        })

        currentIndex += direction
        if (currentIndex < 0) currentIndex = slides.length - 1
        if (currentIndex >= slides.length) currentIndex = 0

        const offset = -currentIndex * 100
        container.style.transform = `translateX(${offset}%)`

        updateDots(carousel, currentIndex)
    }

    function goToSlide(dotButton, index) {
        const carousel = dotButton.closest('.mosaic-testimonials-carousel')
        const container = carousel.querySelector('.carousel-container')
        const offset = -index * 100
        container.style.transform = `translateX(${offset}%)`

        updateDots(carousel, index)
    }

    function updateDots(carousel, activeIndex) {
        const dots = carousel.querySelectorAll('.carousel-dot')
        dots.forEach((dot, index) => {
            if (index === activeIndex) {
                dot.style.backgroundColor = 'var(--mosaic-primary)'
            } else {
                dot.style.backgroundColor = 'var(--mosaic-outline)'
            }
        })
    }
</script>
