{{--
    Modern Process Steps Widget
    
    Props:
    - title (string): Section heading
    - subtitle (string): Section description
    - steps (array): Array of step objects { number, title, description, icon }
    - layout (string): horizontal or vertical layout
    - customizable (bool): Show admin hints
--}}

@props([
    'title' => 'Our Process',
    'subtitle' => 'Four simple steps to get started',
    'steps' => [
        [
            'number' => '1',
            'title' => 'Discovery',
            'description' => 'We learn about your goals and vision',
            'icon' => '🔍',
        ],
        [
            'number' => '2',
            'title' => 'Strategy',
            'description' => 'We create a tailored roadmap',
            'icon' => '📋',
        ],
        [
            'number' => '3',
            'title' => 'Execution',
            'description' => 'We build and deliver results',
            'icon' => '⚙️',
        ],
        [
            'number' => '4',
            'title' => 'Support',
            'description' => 'We provide ongoing assistance',
            'icon' => '🤝',
        ],
    ],
    'layout' => 'horizontal',
    'customizable' => true,
])

<section class="mosaic-process px-6 py-12 md:px-12 md:py-16">
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

    {{-- Steps Container --}}
    @if ($layout === 'horizontal')
        {{-- Horizontal Timeline --}}
        <div class="relative mx-auto max-w-5xl">
            {{-- Timeline Line --}}
            <div
                class="absolute left-0 right-0 top-12 hidden h-1 md:block"
                style="
                    background: linear-gradient(
                        to right,
                        var(--mosaic-primary),
                        var(--mosaic-secondary),
                        var(--mosaic-tertiary)
                    );
                    transform: translateY(-50%);
                "
            ></div>

            {{-- Steps Grid --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                @forelse ($steps as $index => $step)
                    <div class="relative text-center">
                        {{-- Step Circle --}}
                        <div
                            class="relative z-10 mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full"
                            style="
                                background-color: var(
                                    --mosaic-surface-container
                                );
                                border: 3px solid var(--mosaic-primary);
                            "
                        >
                            <div class="text-4xl">
                                {{ $step['icon'] ?? $step['number'] }}
                            </div>
                        </div>

                        {{-- Step Number Badge --}}
                        <div
                            class="absolute right-0 top-0 flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold"
                            style="
                                background-color: var(--mosaic-primary);
                                color: var(--mosaic-on-primary);
                            "
                        >
                            {{ $step['number'] }}
                        </div>

                        {{-- Content --}}
                        @if (isset($step['title']))
                            <h3
                                class="mb-2 text-lg font-bold"
                                style="color: var(--mosaic-on-surface)"
                            >
                                {{ $step['title'] }}
                            </h3>
                        @endif

                        @if (isset($step['description']))
                            <p
                                class="text-sm"
                                style="color: var(--mosaic-on-surface-variant)"
                            >
                                {{ $step['description'] }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <p style="color: var(--mosaic-on-surface-variant)">
                            No steps configured
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        {{-- Vertical Layout --}}
        <div class="mx-auto max-w-3xl space-y-8">
            @forelse ($steps as $index => $step)
                <div class="flex gap-6">
                    {{-- Circle --}}
                    <div
                        class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full text-2xl"
                        style="
                            background-color: var(--mosaic-surface-container);
                            border: 2px solid var(--mosaic-primary);
                        "
                    >
                        {{ $step['icon'] ?? $step['number'] }}
                    </div>

                    {{-- Content --}}
                    <div class="flex-grow">
                        @if (isset($step['title']))
                            <h3
                                class="mb-2 text-lg font-bold"
                                style="color: var(--mosaic-on-surface)"
                            >
                                {{ $step['title'] }}
                            </h3>
                        @endif

                        @if (isset($step['description']))
                            <p
                                class="text-base"
                                style="color: var(--mosaic-on-surface-variant)"
                            >
                                {{ $step['description'] }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p style="color: var(--mosaic-on-surface-variant)">
                        No steps configured
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
                ✨ Customize: Add steps, change icons, titles, and layout
            </span>
        </div>
    @endif
</section>
