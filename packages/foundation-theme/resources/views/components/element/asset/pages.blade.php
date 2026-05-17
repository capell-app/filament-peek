@php
    use Capell\Core\Actions\ResolveRenderableComponentAction;
    use Capell\Core\Enums\AssetComponentEnum;
    use Capell\Core\Enums\RenderableTypeEnum;
    use Capell\Core\Facades\CapellCore;
    use Capell\Frontend\Facades\Frontend;

    $language = Frontend::language();
    $page = Frontend::page();
    $theme = Frontend::theme();
@endphp

@props([
    'columns' => $container['meta']['override_columns'] ?? $element->getMeta('columns', 3),
    'componentItem' => $element->getMeta('component_item', AssetComponentEnum::Card->value),
    'container',
    'containerKey',
    'containerWidth' => null,
    'index',
    'loop',
    'showPageContent' => $elementData['meta']['show_page_content'] ?? false,
    'showPageTitle' => $elementData['meta']['show_page_title'] ?? false,
    'size' => $element->getMeta('size', $containerKey === 'sidebar' ? 'sm' : null),
    'spacing' => $element->getMeta('spacing', $containerKey === 'sidebar' ? 'md' : 'lg'),
    'element',
    'withChildCount' => (bool) $element->getMeta('with_child_count'),
    'withDate' => (bool) $element->getMeta('with_date'),
    'withImage' => (bool) $element->getMeta('with_image'),
    'withParent' => (bool) $element->getMeta('with_parent'),
    'withSummary' => (bool) $element->getMeta('with_summary'),
])
@php
    $pages ??= $element->assets
        ->map(fn (object $elementAsset): ?object => $elementAsset->asset)
        ->filter()
        ->values();

    if ($componentItem === 'capell::list.item') {
        $componentItem = AssetComponentEnum::Card->value;
    }

    $componentItem = ResolveRenderableComponentAction::run(RenderableTypeEnum::Asset, $componentItem);
@endphp

<div class="contents">
    @if ($pages->isNotEmpty() || ! config('capell-layout-builder.element.skip_render_empty', true))
        <x-capell-foundation-theme::element.wrapper
            class="element-pages"
            container-class="space-y-4"
            :$container
            :$containerKey
            :$containerWidth
            :index="$loop->index"
            :$element
        >
            @php
                $showTitle = $element->getMeta("container_options.{$containerKey}.hide_title") !== true
                    && ($element->translation?->title || ($showPageTitle && $page->translation->title));
                $showContent = $element->getMeta("container_options.{$containerKey}.hide_content") !== true
                    && ($element->translation?->content || ($showPageContent && $page->translation->content));
            @endphp

            @if ($showTitle || $showContent)
                <x-capell::content
                    class="element-content"
                    :compact="true"
                    :content="$showContent ? ($element->translation->content ?: ($showPageContent ? $page->translation->content : null)) : null"
                    :content-type="$element->translation->content ? $element->type->content_structure : ($showPageContent ? $page->type->content_structure : null)"
                    :divider="$element->getMeta('content_divider')"
                    :muted="in_array($containerKey, $theme->secondary_containers)"
                    :text-align="$element->getMeta('align')"
                    :title="$showTitle ? ($element->translation->title ?: ($showPageTitle ? $page->translation->title : null)) : null"
                    :heading-style="$element->getMeta('heading_style')"
                    :heading-tag="$showPageTitle ? 'h1' : null"
                />
            @endif

            @if (! $pages || $pages->isEmpty())
                <x-capell::no-results>
                    {!! $element->translation?->getMeta('no_results') ?: __('capell-layout-builder::generic.no_pages_found') !!}
                </x-capell::no-results>
            @else
                <div
                    @class([
                        'grid',
                        ...$containerKey === 'sidebar' && (! $columns && $columns !== 0)
                        ? [
                            'divide-y divide-gray-100 [&>*:not(:first-child)]:pt-4 [&>*:not(:last-child)]:pb-4',
                        ]
                        : [
                            '[&>*:not(:first-child)]:pt-2 [&>*:not(:last-child)]:pb-2' => $spacing === 'sm' && (! $columns && $columns !== 0),
                            '[&>*:not(:first-child)]:pt-4 [&>*:not(:last-child)]:pb-4' => $spacing === 'lg' && (! $columns && $columns !== 0),
                            '[&>*:not(:first-child)]:pt-6 [&>*:not(:last-child)]:pb-6' => $spacing === 'md' && (! $columns && $columns !== 0),
                            '@lg:gap-x-4 @lg:gap-y-4 gap-2' => $spacing === 'sm' && $columns,
                            '@lg:gap-x-8 @lg:gap-y-8 gap-6' => $spacing === 'md' && $columns,
                            '@lg:gap-x-10 @lg:gap-y-10 gap-8' => $spacing === 'lg' && $columns,
                            '@3xl:grid-cols-2' => $columns > 1 && count($pages) >= 2,
                            '@8xl:grid-cols-3' => $columns > 2 && count($pages) >= 3,
                        ],
                    ])
                >
                    @foreach ($pages as $item)
                        <x-dynamic-component
                            :component="$componentItem"
                            :class="$element->key . '-page-item'"
                            :$container
                            :$containerKey
                            :count="$withChildCount ? $item->children_count : null"
                            :icon="(bool) $element->getMeta('icon')"
                            :image="$withImage ? $item->image : null"
                            :$loop
                            :parent="$withParent && method_exists($item, 'loadParent') ? $item->loadParent($language) : null"
                            :publish-date="$withDate ? $item->getPublishDate() : null"
                            :$size
                            :summary="$item->translation->summary"
                            :title="$item->translation->title"
                            :url="$item->pageUrl->full_url"
                            :$withSummary
                        />
                    @endforeach
                </div>

                @if (method_exists($pages, 'total'))
                    <x-capell::pagination :results="$pages" />
                @endif
            @endif
        </x-capell-foundation-theme::element.wrapper>
    @endif
</div>
