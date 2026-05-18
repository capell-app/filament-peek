@props([
    'layout' => $element->getMeta('layout', 'horizontal'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'element',
])

<x-capell-foundation-theme::element.wrapper
    class="capell-modern-process-steps element-ap-process-steps"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <section class="px-6 py-11 md:px-12 md:py-14">
        @if ($element->translation)
            <div class="mx-auto mb-8 max-w-2xl text-center md:mb-10">
                @if ($element->translation->title)
                    <h2
                        class="mb-3 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl"
                    >
                        {{ $element->translation->title }}
                    </h2>
                @endif

                @if ($element->translation->content)
                    <p class="text-lg text-gray-500">
                        {{ strip_tags($element->translation->content) }}
                    </p>
                @endif
            </div>
        @endif

        @if ($layout === 'horizontal')
            <div class="relative mx-auto max-w-5xl">
                <div
                    class="absolute left-0 right-0 top-12 hidden h-px bg-stone-200 md:block"
                ></div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-4 md:gap-6">
                    @forelse ($element->assets as $elementAsset)
                        @php
                            $icon = (string) $elementAsset->asset->getMeta('icon', $loop->index + 1);
                        @endphp

                        <div class="relative text-center">
                            <div class="relative z-10 mx-auto mb-4 h-24 w-24">
                                <div
                                    class="flex h-24 w-24 items-center justify-center rounded-full border-2 border-stone-200 bg-white text-blue-700 shadow-sm"
                                >
                                    @if (str_starts_with($icon, 'heroicon-'))
                                        @svg($icon, 'h-8 w-8')
                                    @else
                                        <span class="text-4xl">
                                            {{ $icon }}
                                        </span>
                                    @endif
                                </div>
                                <div
                                    class="absolute -right-1 -top-1 flex h-7 w-7 items-center justify-center rounded-full bg-stone-800 text-xs font-bold text-white"
                                >
                                    {{ $loop->index + 1 }}
                                </div>
                            </div>

                            @if ($elementAsset->asset->translation?->title)
                                <h3
                                    class="mb-1 text-base font-bold text-gray-900"
                                >
                                    {{ $elementAsset->asset->translation->title }}
                                </h3>
                            @endif

                            @if ($elementAsset->asset->translation?->content)
                                <p class="text-sm text-gray-500">
                                    {{ strip_tags($elementAsset->asset->translation->content) }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center">
                            <p class="text-gray-500">No steps configured.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <div class="mx-auto max-w-3xl space-y-8">
                @forelse ($element->assets as $elementAsset)
                    @php
                        $icon = (string) $elementAsset->asset->getMeta('icon', $loop->index + 1);
                    @endphp

                    <div class="flex gap-6">
                        <div
                            class="relative flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full border-2 border-stone-200 bg-white text-blue-700 shadow-sm"
                        >
                            @if (str_starts_with($icon, 'heroicon-'))
                                @svg($icon, 'h-6 w-6')
                            @else
                                <span class="text-2xl">{{ $icon }}</span>
                            @endif
                            <div
                                class="absolute -right-1 -top-1 flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white"
                            >
                                {{ $loop->index + 1 }}
                            </div>
                        </div>

                        <div class="flex-grow pt-2">
                            @if ($elementAsset->asset->translation?->title)
                                <h3
                                    class="mb-1 text-lg font-bold text-gray-900"
                                >
                                    {{ $elementAsset->asset->translation->title }}
                                </h3>
                            @endif

                            @if ($elementAsset->asset->translation?->content)
                                <p class="text-gray-500">
                                    {{ strip_tags($elementAsset->asset->translation->content) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <p class="text-gray-500">No steps configured.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </section>
</x-capell-foundation-theme::element.wrapper>
