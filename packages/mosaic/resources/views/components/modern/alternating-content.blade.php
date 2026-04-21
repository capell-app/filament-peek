{{--
    Modern Alternating Content Section Widget
    
    Props:
    - title (string): Section heading
    - sections (array): Array of content objects { heading, description, image, position }
    - customizable (bool): Show admin hints
--}}

@props([
    'title' => 'How It Works',
    'sections' => [
        [
            'heading' => 'Step 1: Create',
            'description' => 'Start building your content with our intuitive drag-and-drop editor. No coding required.',
            'image' => '📝',
            'position' => 'left',
        ],
        [
            'heading' => 'Step 2: Customize',
            'description' => 'Personalize colors, fonts, and layouts to match your brand perfectly.',
            'image' => '🎨',
            'position' => 'right',
        ],
        [
            'heading' => 'Step 3: Publish',
            'description' => 'Deploy your content instantly with one click. Real-time updates available.',
            'image' => '🚀',
            'position' => 'left',
        ],
    ],
    'customizable' => true,
])

<section class="mosaic-alternating px-6 py-12 md:px-12 md:py-16">
    {{-- Header --}}
    @if ($title)
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <h2
                class="text-3xl font-bold md:text-4xl"
                style="
                    color: var(--mosaic-on-surface);
                    font-family: var(--mosaic-font-headline);
                "
            >
                {{ $title }}
            </h2>
        </div>
    @endif

    {{-- Content Sections --}}
    <div class="mx-auto max-w-5xl space-y-12">
        @forelse ($sections as $index => $section)
            <div
                class="grid grid-cols-1 items-center gap-8 md:grid-cols-2"
                style="{{ ($section['position'] ?? 'left') === 'right' ? 'direction: rtl;' : '' }}"
            >
                {{-- Image Column --}}
                @if (isset($section['image']))
                    <div
                        class="flex items-center justify-center rounded-lg p-8"
                        style="
                            background-color: var(--mosaic-surface-container);
                            direction: ltr;
                            font-size: 6rem;
                            min-height: 300px;
                        "
                    >
                        {{ $section['image'] }}
                    </div>
                @endif

                {{-- Content Column --}}
                <div style="direction: ltr">
                    {{-- Heading --}}
                    @if (isset($section['heading']))
                        <h3
                            class="mb-4 text-2xl font-bold"
                            style="color: var(--mosaic-on-surface)"
                        >
                            {{ $section['heading'] }}
                        </h3>
                    @endif

                    {{-- Description --}}
                    @if (isset($section['description']))
                        <p
                            class="mb-6 text-base leading-relaxed"
                            style="color: var(--mosaic-on-surface-variant)"
                        >
                            {{ $section['description'] }}
                        </p>
                    @endif

                    {{-- Badge/Number --}}
                    <div
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full font-bold"
                        style="
                            background-color: var(--mosaic-primary);
                            color: var(--mosaic-on-primary);
                        "
                    >
                        {{ $index + 1 }}
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center">
                <p style="color: var(--mosaic-on-surface-variant)">
                    No content sections configured
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
                ✨ Customize: Add sections, change images, toggle positions
            </span>
        </div>
    @endif
</section>
