@php
    use Capell\Core\Enums\AssetComponentEnum;
    use Capell\Core\Facades\CapellCore;
    use Capell\FoundationTheme\Support\ResponsiveAssetLayoutOptions;
    use Capell\Frontend\Contracts\AssetsRegistryInterface;
    use Capell\Frontend\Facades\Frontend;
    use Capell\LayoutBuilder\Enums\ResponsiveLayoutPattern;

    $theme = Frontend::theme();
@endphp

@props([
    'color' => $element->getMeta('color', 'dark'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'element',
    'elementIndex' => null,
    'loop',
    'total' => $element->assets->count(),
    'element' => $element,
    'elementIndex' => $elementIndex,
    'maxWidth' => $element->getMeta('max_width'),
    'withChildCount' => (bool) $element->getMeta('with_child_count'),
    'withImage' => (bool) $element->getMeta('with_image', true),
    'withParent' => (bool) $element->getMeta('with_parent'),
    'withDate' => (bool) $element->getMeta('with_date'),
    'withSummary' => (bool) $element->getMeta('with_summary'),
    'spacing' => $element->getMeta('spacing', true),
    'columns' => (int) $element->getMeta('columns'),
    'headingSize' => $element->getMeta('heading_size'),
    'imagePosition' => $element->getMeta('image_position', 'left'),
    'responsiveLayoutOptions' => ResponsiveAssetLayoutOptions::fromElement($element, $total),
])
@php
    $responsiveLayoutPattern = $responsiveLayoutOptions->pattern;
    $assetLayoutKey = sprintf('%s-%s-%s', $containerKey, $element->id ?? $element->key, $loop->index);
    $assetGridId = "asset-grid-{$assetLayoutKey}";
    $assetCarouselId = "asset-carousel-{$assetLayoutKey}";
    $maxWidthStyle = $maxWidth && ! in_array($maxWidth, ['none', 'sm', 'md', 'lg', 'xl'], true)
        ? '--max-max-width: ' . $maxWidth . ';'
        : '';
@endphp

@if ($element->assets->isNotEmpty() || ! config('capell-layout-builder.element.skip_render_empty', true))
    <x-capell-foundation-theme::element.wrapper
        class="capell-foundation-theme-element-asset element-assets element-assets-grid"
        :$container
        :$containerKey
        :$containerWidth
        :index="$loop->index"
        :element="$element"
        container-class="space-y-6 md:space-y-10"
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
                :text-align="$element->getMeta('align')"
                :heading-style="$element->getMeta('heading_style')"
            />
        @endif

        @if ($element->assets->isNotEmpty())
            @if ($responsiveLayoutPattern->usesMobileCarousel())
                <div
                    wire:ignore
                    data-carousel="1"
                    data-carousel-align="{{ $responsiveLayoutOptions->carouselAlign() }}"
                    data-carousel-autoplay="{{ (int) $responsiveLayoutOptions->carouselAutoPlay }}"
                    data-carousel-autoplay-delay="{{ $responsiveLayoutOptions->carouselAutoDelay }}"
                    data-carousel-disable-on-interaction="{{ (int) $responsiveLayoutOptions->carouselDisableOnInteraction }}"
                    data-carousel-drag="{{ (int) $responsiveLayoutOptions->carouselDrag }}"
                    data-carousel-effect="slide"
                    data-carousel-highlight-active="{{ (int) $responsiveLayoutOptions->highlightActive }}"
                    data-carousel-id="{{ $assetCarouselId }}"
                    data-carousel-loop="{{ (int) $responsiveLayoutOptions->carouselLoop() }}"
                    data-carousel-navigation="{{ (int) $responsiveLayoutOptions->carouselArrows }}"
                    data-carousel-pagination="{{ (int) $responsiveLayoutOptions->carouselPagination }}"
                    data-carousel-pause-on-hover="{{ (int) $responsiveLayoutOptions->carouselPauseOnHover }}"
                    data-carousel-rewind="{{ (int) $responsiveLayoutOptions->carouselRewind }}"
                    data-carousel-rows="{{ $responsiveLayoutOptions->carouselRows }}"
                    data-carousel-speed="{{ $responsiveLayoutOptions->carouselSpeed }}"
                    data-carousel-touch="{{ (int) $responsiveLayoutOptions->carouselTouch }}"
                    data-carousel-watch-overflow="1"
                    data-carousel-breakpoints="{{ $responsiveLayoutOptions->carouselBreakpointsJson() }}"
                    @class([
                        'element-assets-carousel',
                        'md:hidden' => $responsiveLayoutPattern === ResponsiveLayoutPattern::DesktopGridMobileCarousel,
                        'swiper' => $total > 1,
                    ])
                >
                    <div class="swiper-wrapper">
                        @foreach ($element->assets as $asset)
                            <div class="swiper-slide h-auto">
                                <x-dynamic-component
                                    :component="app(AssetsRegistryInterface::class)->getAsset($asset['asset_type'])->component"
                                    :componentItem="$element->getMeta('component_item', AssetComponentEnum::Card->value)"
                                    :$container
                                    :$containerKey
                                    :$loop
                                    :asset="$asset->asset"
                                    :with-child-count="$withChildCount"
                                    :with-date="$withDate"
                                    :with-image="$withImage"
                                    :heading-size="$headingSize"
                                    :image-position="$imagePosition"
                                    :with-parent="$withParent"
                                    :with-summary="$withSummary"
                                    class="element-asset h-full"
                                />
                            </div>
                        @endforeach
                    </div>

                    @if ($total > 1)
                        <div
                            data-carousel-controls="{{ $assetCarouselId }}"
                            class="swiper-controls mt-4 flex justify-center"
                        >
                            <div
                                class="swiper-pagination pointer-events-auto flex justify-center"
                            ></div>
                        </div>
                    @endif
                </div>
            @endif

            @if ($responsiveLayoutPattern->usesDesktopGrid())
                @if ($responsiveLayoutOptions->shouldUseResponsiveGrid())
                    {!! $responsiveLayoutOptions->gridRowsStyle($assetGridId) !!}
                @endif

                <div
                    id="{{ $assetGridId }}"
                    style="{{ $responsiveLayoutOptions->shouldUseResponsiveGrid() ? $responsiveLayoutOptions->gridColumnsStyle($maxWidthStyle) : '--columns: ' . ($columns ?: $total) . ';' . $maxWidthStyle }}"
                    @class([
                        'grid',
                        'md:grid-cols-[repeat(var(--columns),minmax(0,1fr))]' => ! $responsiveLayoutOptions->shouldUseResponsiveGrid(),
                        'sm:grid-cols-[repeat(var(--columns-sm),minmax(0,1fr))] md:grid-cols-[repeat(var(--columns-md),minmax(0,1fr))] lg:grid-cols-[repeat(var(--columns-lg),minmax(0,1fr))] xl:grid-cols-[repeat(var(--columns-xl),minmax(0,1fr))]' => $responsiveLayoutOptions->shouldUseResponsiveGrid(),
                        'hidden md:grid' => $responsiveLayoutPattern === ResponsiveLayoutPattern::DesktopGridMobileCarousel,
                        'mx-auto' => $maxWidth,
                        $maxWidth ? match ($maxWidth) {
                            'none' => 'max-w-none',
                            'sm' => 'max-w-sm',
                            'md' => 'max-w-md',
                            'lg' => 'max-w-lg',
                            'xl' => 'max-w-xl',
                            '2xl' => 'max-w-2xl',
                            '3xl' => 'max-w-3xl',
                            default => 'max-w-[var(--max-max-width)]',
                        } : '',
                        'gap-x-8 gap-y-6 lg:gap-x-10 lg:gap-y-10' => $spacing && $spacing !== 'none',
                        'sm:grid-cols-2' => ! $responsiveLayoutOptions->shouldUseResponsiveGrid() && $total >= 2 && $columns === 0,
                        'md:grid-cols-2' => ! $responsiveLayoutOptions->shouldUseResponsiveGrid() && $total >= 2 && $columns !== 0 && $total <= $columns,
                        'lg:grid-cols-4' => ! $responsiveLayoutOptions->shouldUseResponsiveGrid() && $total >= 4 && $columns !== 0 && $total <= $columns,
                        '2xl:grid-cols-6' => ! $responsiveLayoutOptions->shouldUseResponsiveGrid() && $total >= 6 && $columns !== 0 && $total <= $columns,
                    ])
                >
                    @foreach ($element->assets as $asset)
                        <x-dynamic-component
                            :component="app(AssetsRegistryInterface::class)->getAsset($asset['asset_type'])->component"
                            :componentItem="$element->getMeta('component_item', AssetComponentEnum::Card->value)"
                            :$container
                            :$containerKey
                            :$loop
                            :asset="$asset->asset"
                            :with-child-count="$withChildCount"
                            :with-date="$withDate"
                            :with-image="$withImage"
                            :heading-size="$headingSize"
                            :image-position="$imagePosition"
                            :with-parent="$withParent"
                            :with-summary="$withSummary"
                            class="element-asset"
                        />
                    @endforeach
                </div>
            @endif
        @endif
    </x-capell-foundation-theme::element.wrapper>
@endif
