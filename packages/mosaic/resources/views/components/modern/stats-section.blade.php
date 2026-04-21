{{--
    Modern Stats Section Widget
    
    Props:
    - title (string): Section heading
    - subtitle (string): Section description
    - stats (array): Array of stat objects { icon, label, value, color }
    - layout (string): horizontal or vertical layout
    - customizable (bool): Show admin hints
--}}

@props([
    'title' => 'By The Numbers',
    'subtitle' => 'Proven results that speak for themselves',
    'stats' => [
        [
            'icon' => '👥',
            'label' => 'Active Users',
            'value' => '10,000+',
            'color' => 'primary',
        ],
        [
            'icon' => '🚀',
            'label' => 'Projects Launched',
            'value' => '500+',
            'color' => 'secondary',
        ],
        [
            'icon' => '⭐',
            'label' => 'Satisfaction Rate',
            'value' => '98%',
            'color' => 'tertiary',
        ],
        [
            'icon' => '🌍',
            'label' => 'Countries',
            'value' => '50+',
            'color' => 'primary',
        ],
    ],
    'layout' => 'horizontal',
    'customizable' => true,
])

<section class="mosaic-stats px-6 py-12 md:px-12 md:py-16">
    {{-- Header --}}
    @if ($title)
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <h2
                class="mb-3 text-3xl font-bold md:text-4xl"
                style="
                    color: var(--mosaic-on-surface);
                    font-family: var(--mosaic-font-headline);
                "
            >
                {{ $title }}
            </h2>
            @if ($subtitle)
                <p
                    class="text-lg"
                    style="color: var(--mosaic-on-surface-variant)"
                >
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif

    {{-- Stats Grid --}}
    <div
        class="{{ $layout === 'vertical' ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4' }} mx-auto grid max-w-6xl gap-6"
    >
        @forelse ($stats as $stat)
            <div
                class="mosaic-card p-8 text-center"
                style="background-color: var(--mosaic-surface-container)"
            >
                {{-- Icon --}}
                @if (isset($stat['icon']))
                    <div class="mb-4 text-5xl">
                        {{ $stat['icon'] }}
                    </div>
                @endif

                {{-- Value --}}
                @if (isset($stat['value']))
                    <p
                        class="mb-2 text-4xl font-bold"
                        style="
                            color: var(
                                --mosaic-{{ $stat['color'] ?? 'primary' }}
                            );
                        "
                    >
                        {{ $stat['value'] }}
                    </p>
                @endif

                {{-- Label --}}
                @if (isset($stat['label']))
                    <p
                        class="text-base font-semibold"
                        style="color: var(--mosaic-on-surface-variant)"
                    >
                        {{ $stat['label'] }}
                    </p>
                @endif
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <p style="color: var(--mosaic-on-surface-variant)">
                    No stats configured
                </p>
            </div>
        @endforelse
    </div>

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
                ✨ Customize: Add stats, change icons, values, and layout
            </span>
        </div>
    @endif
</section>
