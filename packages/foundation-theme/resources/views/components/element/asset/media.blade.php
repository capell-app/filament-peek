@php
    use Capell\Core\Enums\ContainerWidthEnum;
    use Capell\FoundationTheme\Actions\BuildElementAssetRenderDataAction;
    use Capell\Frontend\Facades\Frontend;
    use Illuminate\Support\Str;

    $theme = Frontend::theme();
@endphp

@props([
    'color' => $element->getMeta('color', 'dark'),
    'columns' => $container['meta']['override_columns'] ?? $element->getMeta('columns', 4),
    'container',
    'containerKey',
    'containerWidth' => null,
    'large' => false,
    'loop',
    'size' => $element->getMeta('size'),
    'spacing' => $element->getMeta('spacing'),
    'element',
    'element_theme' => $element->getMeta('element_theme'),
])
@if ($element->assets->isNotEmpty() || ! config('capell-layout-builder.element.skip_render_empty', true))
    <x-capell-foundation-theme::element.wrapper
        :class="'element-media-gallery' . ($containerWidth === ContainerWidthEnum::Full ? ' px-4' : '')"
        :$container
        :$containerKey
        :$containerWidth
        :index="$loop->index"
        :$element
    >
        @if ($element->translation)
            <x-capell::content
                :class="'mb-5' . ($containerWidth === ContainerWidthEnum::Full ? ' container' : '')"
                :compact="true"
                align="center"
                :content="$element->translation->content"
                :content-type="$element->type->content_structure"
                :color="$color"
                :divider="$element->getMeta('content_divider')"
                :muted="in_array($containerKey, $theme->secondary_containers)"
                :title="$element->translation->title"
                :text-align="$element->getMeta('align', 'center')"
                :heading-style="$element->getMeta('heading_style')"
            />
        @endif

        @if ($element->assets->isNotEmpty())
            <div
                @class([
                    'grid grid-cols-2 2xl:container md:grid-cols-3',
                    'gap-2' => $spacing === 'sm',
                    'gap-4' => $spacing === 'md',
                    'gap-6' => $spacing === 'lg',
                ])
            >
                @foreach ($element->assets as $elementAsset)
                    {{-- format-ignore-start --}}
                @php
                    $assetRenderData = BuildElementAssetRenderDataAction::run($elementAsset);
                    $image = $assetRenderData->image;
                    if (! $image) {
                        report('Image not found for ElementAsset: ' . $elementAsset->asset_type . ' ' . $elementAsset->id);
                        continue;
                    }
                @endphp
                {{-- format-ignore-end --}}
                    <div
                        @class([
                            'element-media-item group relative h-full cursor-pointer overflow-hidden text-center',
                            'md:col-span-1 md:row-span-2' => ($loop->iteration > 5 && $loop->iteration % 5 === 0) || $loop->iteration === 2,
                        ])
                        tabindex="0"
                    >
                        @if (Str::startsWith($image->mime_type, 'video/'))
                            <x-capell::media
                                :class="'h-full w-full bg-gray-50 object-cover shadow transition-transform duration-300 group-hover:scale-105 group-focus-within:scale-105' . ($theme->withDarkMode ? ' dark:bg-gray-800' : '')"
                                :height="$large ? 600 : 300"
                                :$loop
                                :media="$image"
                                :preview="(int) $image->getMeta('image_id')"
                                :alt="$assetRenderData->alt"
                                :width="440"
                                media_type="video"
                                fit="crop-center"
                                lightbox="true"
                            />
                        @else
                            <x-capell::media
                                :class="'h-full w-full bg-gray-50 object-cover shadow transition-transform duration-300 group-hover:scale-105 group-focus-within:scale-105' . ($theme->withDarkMode ? ' dark:bg-gray-800' : '')"
                                :height="$large ? 600 : 300"
                                :$loop
                                :media="$image"
                                :alt="$assetRenderData->alt"
                                :width="440"
                                fit="crop-center"
                                lightbox="true"
                            />
                        @endif

                        @if ($assetRenderData->title)
                            <div
                                @class([
                                    'pointer-events-none absolute inset-x-0 bottom-0 flex items-center justify-center
                                break-words bg-gray-600/75 px-2 py-4 font-medium leading-none leading-tight text-white
                                transform translate-y-full opacity-0 transition-all duration-300
                                group-hover:translate-y-0 group-hover:opacity-100
                                group-focus-within:translate-y-0 group-focus-within:opacity-100',
                                    'text-sm' => $size === 'sm',
                                    'text-lg' => $size === 'lg',
                                    'rounded-b' => (bool) $theme->getMeta('rounded_images'),
                                ])
                            >
                                {{ $assetRenderData->title }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-capell-foundation-theme::element.wrapper>
@endif
