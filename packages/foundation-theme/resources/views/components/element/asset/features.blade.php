@php
    use Capell\Core\Contracts\Pageable;
    use Capell\Core\Enums\AssetComponentEnum;
    use Capell\Core\Facades\CapellCore;
    use Capell\Core\Models\Page;
    use Capell\Frontend\Facades\Frontend;

    $theme = Frontend::theme();
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
])

@if ($element->assets->isNotEmpty() || ! config('capell-layout-builder.element.skip_render_empty', true))
    <x-capell-foundation-theme::element.wrapper
        class="element-assets element-assets-features"
        :$container
        :$containerKey
        :$containerWidth
        container-class="space-y-6 md:space-y-10"
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
                :text-align="$element->getMeta('align')"
                :heading-style="$element->getMeta('heading_style')"
                align="center"
            />
        @endif

        @if ($element->assets->isNotEmpty())
            <div
                @class([
                    'grid grid-cols-1 items-start gap-x-10 gap-y-6 md:grid-cols-2',
                    'lg:grid-cols-3' => $element->image,
                ])
            >
                @if ($element->image)
                    <div
                        class="flex min-h-full justify-center md:col-span-2 lg:order-2 lg:col-span-1"
                    >
                        <x-capell::media
                            :media="$element->image"
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
                    @foreach ($element->assets->slice(0, ceil($element->assets->count() / 2)) as $elementAsset)
                        <x-capell-foundation-theme::element.asset.feature-item
                            :$color
                            column="1"
                            :$element
                            :$elementAsset
                        />
                    @endforeach
                </div>

                <div
                    class="grid space-y-6 md:min-h-full md:auto-rows-fr lg:order-3 lg:space-y-8"
                >
                    @foreach ($element->assets->slice(ceil($element->assets->count() / 2)) as $elementAsset)
                        <x-capell-foundation-theme::element.asset.feature-item
                            :$color
                            column="2"
                            :$element
                            :$elementAsset
                        />
                    @endforeach
                </div>
            </div>
        @endif
    </x-capell-foundation-theme::element.wrapper>
@endif
