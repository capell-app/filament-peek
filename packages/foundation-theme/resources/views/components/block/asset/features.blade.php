@php
    use Capell\Core\Contracts\Pageable;
    use Capell\Core\Enums\AssetComponentEnum;
    use Capell\Core\Facades\CapellCore;
    use Capell\Core\Models\Page;
    use Capell\Frontend\Facades\Frontend;

    $theme = Frontend::theme();
@endphp

@props([
    'color' => $block->getMeta('color', 'dark'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'total' => $block->assets->count(),
    'block',
    'blockIndex',
    'withChildCount' => (bool) $block->getMeta('with_child_count'),
    'withImage' => (bool) $block->getMeta('with_image', true),
    'withParent' => (bool) $block->getMeta('with_parent'),
    'withDate' => (bool) $block->getMeta('with_date'),
    'withSummary' => (bool) $block->getMeta('with_summary'),
])

@if ($block->assets->isNotEmpty() || ! config('capell-layout-builder.block.skip_render_empty', true))
    <x-capell-foundation-theme::block.wrapper
        class="capell-asset-features block-assets block-assets-features"
        :$container
        :$containerKey
        :$containerWidth
        container-class="space-y-6 md:space-y-10"
        :index="$loop->index"
        :$block
    >
        @if ($block->translation)
            <x-capell::content
                :compact="true"
                :content="$block->translation->content"
                :content-type="$block->type->content_structure"
                :color="$color"
                :divider="$block->getMeta('content_divider')"
                :muted="in_array($containerKey, $theme->secondary_containers)"
                :title="$block->translation->title"
                :text-align="$block->getMeta('align')"
                :heading-style="$block->getMeta('heading_style')"
                align="center"
            />
        @endif

        @if ($block->assets->isNotEmpty())
            <div
                @class([
                    'grid grid-cols-1 items-start gap-x-10 gap-y-6 md:grid-cols-2',
                    'lg:grid-cols-3' => $block->image,
                ])
            >
                @if ($block->image)
                    <div
                        class="flex min-h-full justify-center md:col-span-2 lg:order-2 lg:col-span-1"
                    >
                        <x-capell::media
                            :media="$block->image"
                            format="webp"
                            size="xl"
                            fit="fit"
                            loading="lazy"
                            class="object-cover"
                        />
                    </div>
                @endif

                <div
                    class="grid space-y-6 md:min-h-full md:auto-rows-fr lg:order-1 lg:space-y-8"
                >
                    @foreach ($block->assets->slice(0, ceil($block->assets->count() / 2)) as $blockAsset)
                        <x-capell-foundation-theme::block.asset.feature-item
                            :$color
                            column="1"
                            :$block
                            :$blockAsset
                        />
                    @endforeach
                </div>

                <div
                    class="grid space-y-6 md:min-h-full md:auto-rows-fr lg:order-3 lg:space-y-8"
                >
                    @foreach ($block->assets->slice(ceil($block->assets->count() / 2)) as $blockAsset)
                        <x-capell-foundation-theme::block.asset.feature-item
                            :$color
                            column="2"
                            :$block
                            :$blockAsset
                        />
                    @endforeach
                </div>
            </div>
        @endif
    </x-capell-foundation-theme::block.wrapper>
@endif
