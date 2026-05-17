@php
    use Capell\Frontend\Facades\Frontend;
    use Capell\Navigation\Actions\BuildNavigationRenderModelAction;
    use Capell\Navigation\Data\NavigationItemRenderData;
    use Capell\Navigation\Data\NavigationRenderContextData;
    use Capell\Navigation\Data\NavigationRenderData;
    use Capell\Navigation\Models\Navigation;
    use Capell\Navigation\Support\Loader\NavigationLoader;
    use Illuminate\Support\Collection;

    $theme = Frontend::theme();

    if (! isset($menu)) {
        $menu = null;

        if (isset($element->meta['navigation_id']) && is_numeric($element->meta['navigation_id'])) {
            $menu = NavigationLoader::getNavigationById($element->meta['navigation_id']);
        } elseif (isset($element->meta['navigation']) && is_string($element->meta['navigation'])) {
            $menu = NavigationLoader::getNavigation(
                $element->meta['navigation'],
                Frontend::site(),
                Frontend::language(),
            );
        }
    }

    if (! isset($navigationRenderData)) {
        $navigationRenderData = null;
        if ($menu instanceof Navigation) {
            $navigationRenderData = BuildNavigationRenderModelAction::run(new NavigationRenderContextData(
                navigation: $menu,
                page: Frontend::page(),
                site: Frontend::site(),
                language: Frontend::language(),
                siteDomain: Frontend::site()->siteDomain,
            ));
        }
    }

    if (! isset($items)) {
        $items = $navigationRenderData instanceof NavigationRenderData ? $navigationRenderData->items : collect();
    }

    $listComponent = $navigationRenderData instanceof NavigationRenderData
        ? $navigationRenderData->listComponent
        : ($menu instanceof Navigation ? $menu->getMeta('component', 'capell::list') : 'capell::list');
@endphp

@props([
    'columns' => $container['meta']['override_columns'] ?? $element->getMeta('columns', 3),
    'container',
    'containerKey',
    'containerWidth' => null,
    'groupItems' => $elementData['meta']['group_items'] ?? false,
    'showPageContent' => $elementData['meta']['show_page_content'] ?? false,
    'showPageTitle' => $elementData['meta']['show_page_title'] ?? false,
    'items' => [],
    'loop',
    'element',
])
@if ($items->isNotEmpty() || ! config('capell-layout-builder.element.skip_render_empty', true))
    <x-capell-foundation-theme::element.wrapper
        class="element-navigation"
        :$container
        :$containerKey
        :$containerWidth
        :index="$loop->index"
        :$element
    >
        @if (($element->translation && ($element->translation->title || $element->translation->content))
             || ($showPageTitle && $page->translation->title)
             || ($showPageContent && $page->translation->content))
            <x-capell::content
                class="mb-5"
                :compact="true"
                :content="$element->translation->content ?? ($showPageContent ? $page->translation->content : null)"
                :content-type="$element->translation->content ? $element->type->content_structure : ($showPageContent ? $page->type->content_structure : null)"
                :divider="$element->getMeta('content_divider')"
                :muted="in_array($containerKey, $theme->secondary_containers)"
                :text-align="$element->getMeta('align')"
                :title="$element->translation->title ?? ($showPageTitle ? $page->translation->title : null)"
                :heading-style="$element->getMeta('heading_style')"
                :heading-tag="$showPageTitle ? 'h1' : null"
            />
        @endif

        @if ($groupItems && count($items) > 5)
            <div class="grid md:grid-cols-2">
                @php
                    /**
                     * @var Collection<NavigationItemRenderData> $items
                     */
                    $half = (int) ceil(count($items) / $columns);

                    /**
                     * @var Collection<Collection<NavigationItemRenderData>> $chunks
                     */
                    $chunks = $items->chunk($half);
                @endphp

                @foreach ($chunks as $chunk)
                    <x-dynamic-component
                        :component="$listComponent"
                        class="element-navigation-list"
                    >
                        @foreach ($chunk as $item)
                            <x-dynamic-component
                                :component="$item instanceof NavigationItemRenderData ? ($item->componentItem ?: 'capell::list.item') : (! empty($item->data['component_item']) ? $item->data['component_item'] : 'capell::list.item')"
                                class="element-navigation-item"
                                :$item
                            />
                        @endforeach
                    </x-dynamic-component>
                @endforeach
            </div>
        @else
            <x-dynamic-component
                :component="$listComponent"
                class="element-navigation-list element-navigation-lit-children text-sm"
            >
                @foreach ($items as $item)
                    <x-dynamic-component
                        :component="$item instanceof NavigationItemRenderData ? ($item->componentItem ?: 'capell::list.item') : (! empty($item->data['component_item']) ? $item->data['component_item'] : 'capell::list.item')"
                        :$item
                        class="element-navigation-item element-navigation-child-item"
                    />
                @endforeach
            </x-dynamic-component>
        @endif
    </x-capell-foundation-theme::element.wrapper>
@endif
