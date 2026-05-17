@php
    use Capell\Core\Facades\CapellCore;
    use Capell\FoundationTheme\Actions\BuildElementAssetRenderDataAction;
    use Capell\Frontend\Facades\Frontend;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;

    $page = Frontend::page();
    $theme = Frontend::theme();
@endphp

@props([
    'align' => $element->getMeta('align', 'center'),
    'carouselArrows' => (bool) $element->getMeta('carousel_arrows', false),
    'carouselFade' => $element->getMeta('carousel_fade', true),
    'carouselAutoPlay' => $element->getMeta('carousel_auto_play', true),
    'carouselAutoDelay' => $element->getMeta('carousel_auto_delay', 5000),
    'carouselDisableOnInteraction' => (bool) $element->getMeta('carousel_disable_on_interaction', true),
    'carouselDrag' => (bool) $element->getMeta('carousel_drag', false),
    'carouselEffect' => $element->getMeta('carousel_effect', 'slide'),
    'carouselLoop' => $element->getMeta('carousel_loop', true),
    'carouselPagination' => $element->getMeta('carousel_pagination', true),
    'carouselPauseOnHover' => (bool) $element->getMeta('carousel_pause_on_hover', true),
    'carouselRewind' => (bool) $element->getMeta('carousel_rewind', false),
    'carouselSpeed' => (int) $element->getMeta('carousel_speed', 300),
    'carouselTouch' => $element->getMeta('carousel_touch'),
    'carouselWheel' => (bool) $element->getMeta('carousel_wheel', false),
    'color' => $element->getMeta('color', 'light'),
    'containerKey',
    'containerIndex',
    'containerWidth',
    'loop',
    'total' => $element->assets->count(),
    'element',
    'elementIndex',
])
@php
    $carouselId = sprintf('testimonial-carousel-%s-%s', $element->id ?? $element->key, $loop->index);
    $carouselEffect = $carouselFade ? 'fade' : $carouselEffect;
@endphp

@if ($element->assets->isNotEmpty() || ! config('capell-layout-builder.element.skip_render_empty', true))
    <x-capell-foundation-theme::element.wrapper
        class="element-assets element-assets-testimonials"
        :$container
        :$containerKey
        :$containerWidth
        container-class="relative py-6 space-y-6 md:space-y-10 lg:py-16"
        :index="$loop->index"
        :$element
    >
        @if ($element->translation)
            <x-capell::content
                :compact="true"
                :content="$element->translation->content"
                :content-type="$element->type->content_structure"
                :color="$color"
                :divider="$element->getMeta('content_divider')"
                :muted="in_array($containerKey, $theme->secondary_containers)"
                :title="$element->translation->title"
                heading-weight="semibold"
                :text-align="$align"
                :heading-style="$element->getMeta('heading_style')"
                class="mt-4"
            />
        @endif

        @if ($element->assets->isNotEmpty())
            <div
                @class([
                    'relative',
                    'pb-4' => $total > 1,
                ])
                style="
                    --swiper-pagination-bottom: auto;
                    --swiper-pagination-top: 100%;
                    --swiper-pagination-bullet-inactive-color: #fff;
                "
            >
                <div
                    data-carousel="1"
                    data-carousel-align="{{ $align }}"
                    data-carousel-autoplay="{{ (int) $carouselAutoPlay }}"
                    data-carousel-autoplay-delay="{{ $carouselAutoDelay }}"
                    data-carousel-disable-on-interaction="{{ (int) $carouselDisableOnInteraction }}"
                    data-carousel-drag="{{ (int) $carouselDrag }}"
                    data-carousel-effect="{{ $carouselEffect }}"
                    data-carousel-id="{{ $carouselId }}"
                    data-carousel-loop="{{ (int) $carouselLoop }}"
                    data-carousel-navigation="{{ (int) $carouselArrows }}"
                    data-carousel-pagination="{{ (int) $carouselPagination }}"
                    data-carousel-pause-on-hover="{{ (int) $carouselPauseOnHover }}"
                    data-carousel-rewind="{{ (int) $carouselRewind }}"
                    data-carousel-speed="{{ $carouselSpeed }}"
                    data-carousel-watch-overflow="1"
                    data-carousel-wheel="{{ (int) $carouselWheel }}"
                    data-auto="{{ (int) $carouselAutoPlay }}"
                    data-loop="{{ (int) $carouselLoop }}"
                    data-delay="{{ $carouselAutoDelay }}"
                    data-fade="{{ $carouselFade }}"
                    @if ($carouselTouch !== null)
                        data-carousel-touch="{{ (int) $carouselTouch }}"
                    @endif
                    class="swiper grid h-full w-full"
                >
                    <div class="swiper-wrapper h-full w-full">
                        @foreach ($element->assets as $elementAsset)
                            {{-- format-ignore-start --}}
                        @php
                            $title = '';
                            $content = '';
                            $assetRenderData = BuildElementAssetRenderDataAction::run($elementAsset);
                            $media = $assetRenderData->image;

                            $position = is_object($assetRenderData->translation) && method_exists($assetRenderData->translation, 'getMeta')
                                ? $assetRenderData->translation->getMeta('position', '')
                                : '';
                            $company = is_object($assetRenderData->translation) && method_exists($assetRenderData->translation, 'getMeta')
                                ? $assetRenderData->translation->getMeta('company', '')
                                : '';

                            if (CapellCore::getAsset($elementAsset->asset_type)->hasTranslations) {
                                $title = $assetRenderData->title;
                                $content = $assetRenderData->content;
                            }
                        @endphp
                        {{-- format-ignore-end --}}

                            <div
                                class="swiper-slide element-testimonial-item"
                                itemscope
                                itemtype="https://schema.org/Review"
                            >
                                <div
                                    @class([
                                        'relative flex w-full shrink-0 basis-full flex-col space-y-4',
                                        'items-center justify-center text-center' => $align === 'center',
                                        'items-start justify-start text-left' => $align === 'left',
                                        'items-end justify-end text-right' => $align === 'right',
                                    ])
                                >
                                    @if ($media)
                                        <x-capell::media
                                            :media="$media"
                                            :alt="$assetRenderData->alt"
                                            rounded="full"
                                            class="h-20 w-20 object-cover"
                                            itemprop="image"
                                        />
                                    @endif

                                    @if ($content)
                                        <blockquote
                                            class="lg:text-md max-w-2xl italic text-white"
                                            itemprop="reviewBody"
                                        >
                                            {!! $content !!}
                                        </blockquote>
                                    @endif

                                    @if ($title)
                                        <div>
                                            <div
                                                class="text-sm font-bold text-white lg:text-base"
                                                itemprop="author"
                                                itemscope
                                                itemtype="https://schema.org/Person"
                                            >
                                                <span itemprop="name">
                                                    {{ $title }}
                                                </span>
                                            </div>

                                            @if ($position || $company)
                                                <div
                                                    class="text-smaller block font-normal text-gray-300"
                                                >
                                                    <span itemprop="jobTitle">
                                                        {{ $position }}
                                                    </span>
                                                    @if ($company)
                                                        @if ($position)
                                                            <span class="mx-1">
                                                                |
                                                            </span>
                                                        @endif

                                                        <span
                                                            itemprop="worksFor"
                                                        >
                                                            {{ $company }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if ($total > 1)
                    <div
                        data-carousel-controls="{{ $carouselId }}"
                        class="swiper-controls space-y-4"
                    >
                        @if ($carouselArrows)
                            <div class="flex items-center justify-center gap-3">
                                <button
                                    aria-label="{{ __('capell-frontend::generic.previous') }}"
                                    class="swiper-button-prev pointer-events-auto relative inset-auto m-0 flex h-10 w-10 items-center justify-center rounded-full bg-white/80 text-gray-900 shadow-md transition hover:bg-white"
                                    type="button"
                                ></button>
                                <button
                                    aria-label="{{ __('capell-frontend::generic.next') }}"
                                    class="swiper-button-next pointer-events-auto relative inset-auto m-0 flex h-10 w-10 items-center justify-center rounded-full bg-white/80 text-gray-900 shadow-md transition hover:bg-white"
                                    type="button"
                                ></button>
                            </div>
                        @endif

                        <div
                            class="swiper-pagination flex justify-center"
                            wire:ignore
                        ></div>
                    </div>
                @endif
            </div>
        @endif
    </x-capell-foundation-theme::element.wrapper>
@endif
