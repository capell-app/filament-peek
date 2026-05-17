<?php
use Capell\Frontend\Facades\Frontend;

$theme = Frontend::theme();
?>

@php
    use Capell\Core\Enums\AssetComponentEnum;
    use Capell\Core\Facades\CapellCore;
    use Capell\FoundationTheme\Actions\BuildElementAssetRenderDataAction;
    use Capell\Frontend\Contracts\AssetsRegistryInterface;
@endphp

@props([
    'color' => $element->getMeta('color', 'dark'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'total' => $element->assets->count(),
    'element',
    'elementIndex',
    'withChildCount' => (bool) $element->getMeta('with_child_count'),
    'withImage' => (bool) $element->getMeta('with_image', true),
    'withParent' => (bool) $element->getMeta('with_parent'),
    'withDate' => (bool) $element->getMeta('with_date'),
    'withSummary' => (bool) $element->getMeta('with_summary'),
    'spacing' => $element->getMeta('spacing', true),
    'columns' => (int) $element->getMeta('columns'),
])

@if ($element->assets->isNotEmpty() || ! config('capell-layout-builder.element.skip_render_empty', true))
    <x-capell-foundation-theme::element.wrapper
        class="element-assets-blocks relative"
        :$container
        :$containerKey
        :$containerWidth
        :index="$loop->index"
        :$element
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
            <div>
                @php
                    $firstAssetRenderData = BuildElementAssetRenderDataAction::run($element->assets->first());
                    $lastAssetRenderData = BuildElementAssetRenderDataAction::run($element->assets->last());
                @endphp

                @if ($color = ($firstAssetRenderData->meta['color'] ?? null))
                    <x-capell-foundation-theme::element.asset.extended-background
                        :$color
                        position="left"
                    />
                @endif

                <div
                    style="--columns: {{ $columns ?: $total }}"
                    @class([
                        'grid md:grid-cols-[repeat(var(--columns),minmax(0,1fr))]',
                        'gap-x-8 gap-y-6 lg:gap-x-10 lg:gap-y-10' => $spacing && $spacing !== 'none',
                        'sm:grid-cols-2' => $total >= 2 && $columns === 0,
                        'md:grid-cols-2' => $total >= 2 && $columns !== 0 && $total <= $columns,
                        'lg:grid-cols-4' => $total >= 4 && $columns !== 0 && $total <= $columns,
                        '2xl:grid-cols-6' => $total >= 6 && $columns !== 0 && $total <= $columns,
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
                            :with-parent="$withParent"
                            :with-summary="$withSummary"
                            class="element-block-item"
                        />
                    @endforeach
                </div>
                @if ($color = ($lastAssetRenderData->meta['color'] ?? null))
                    <x-capell-foundation-theme::element.asset.extended-background
                        :$color
                        position="right"
                    />
                @endif
            </div>
        @endif
    </x-capell-foundation-theme::element.wrapper>
@endif
