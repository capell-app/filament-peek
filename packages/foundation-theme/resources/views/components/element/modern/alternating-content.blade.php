@props([
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'element',
])

@php
    use Capell\FoundationTheme\Actions\BuildElementAssetRenderDataAction;
@endphp

<x-capell-foundation-theme::element.wrapper
    class="element-ap-alternating-content"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <section class="px-6 py-12 md:px-12 md:py-16">
        @if ($element->translation)
            <div class="mx-auto mb-12 max-w-2xl text-center">
                @if ($element->translation->title)
                    <h2 class="text-3xl font-bold text-gray-900 md:text-4xl">
                        {{ $element->translation->title }}
                    </h2>
                @endif

                @if ($element->translation->content)
                    <p class="mt-3 text-lg text-gray-500">
                        {{ strip_tags($element->translation->content) }}
                    </p>
                @endif
            </div>
        @endif

        <div class="mx-auto max-w-5xl space-y-16">
            @forelse ($element->assets as $elementAsset)
                @php
                    $assetRenderData = BuildElementAssetRenderDataAction::run($elementAsset);
                    $isRight = $assetRenderData->position === 'right';
                    $icon = (string) ($assetRenderData->icon ?? '');
                    $media = $assetRenderData->image;
                @endphp

                <div class="grid grid-cols-1 items-center gap-8 md:grid-cols-2">
                    <div
                        @class([
                            'flex min-h-64 items-center justify-center rounded-2xl bg-gray-50 p-8',
                            'md:order-last' => $isRight,
                        ])
                    >
                        @if ($icon !== '')
                            <span class="text-blue-700">
                                @if (str_starts_with($icon, 'heroicon-'))
                                    @svg($icon, 'h-20 w-20')
                                @else
                                    <span class="text-8xl">{{ $icon }}</span>
                                @endif
                            </span>
                        @elseif ($media)
                            <img
                                src="{{ $media->getFullUrl() }}"
                                alt="{{ $assetRenderData->title }}"
                                class="h-full w-full rounded-xl object-cover"
                            />
                        @endif
                    </div>

                    <div>
                        <div
                            class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white"
                        >
                            {{ $loop->index + 1 }}
                        </div>

                        @if ($assetRenderData->title)
                            <h3 class="mb-3 text-2xl font-bold text-gray-900">
                                {{ $assetRenderData->title }}
                            </h3>
                        @endif

                        @if ($assetRenderData->content)
                            <p class="text-base leading-relaxed text-gray-600">
                                {{ strip_tags($assetRenderData->content) }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-gray-500">No content sections configured.</p>
                </div>
            @endforelse
        </div>
    </section>
</x-capell-foundation-theme::element.wrapper>
