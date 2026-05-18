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

        if (isset($block->meta['navigation_id']) && is_numeric($block->meta['navigation_id'])) {
            $menu = NavigationLoader::getNavigationById($block->meta['navigation_id']);
        } elseif (isset($block->meta['navigation']) && is_string($block->meta['navigation'])) {
            $menu = NavigationLoader::getNavigation(
                $block->meta['navigation'],
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
    'columns' => $container['meta']['override_columns'] ?? $block->getMeta('columns', 3),
    'container',
    'containerKey',
    'containerWidth' => null,
    'groupItems' => $blockData['meta']['group_items'] ?? false,
    'showPageContent' => $blockData['meta']['show_page_content'] ?? false,
    'showPageTitle' => $blockData['meta']['show_page_title'] ?? false,
    'items' => [],
    'loop',
    'block',
])
@if ($items->isNotEmpty() || ! config('capell-layout-builder.block.skip_render_empty', true))
    <x-capell-foundation-theme::block.wrapper
        class="capell-block-navigation block-navigation"
        :$container
        :$containerKey
        :$containerWidth
        :index="$loop->index"
        :$block
    >
        @if (($block->translation && ($block->translation->title || $block->translation->content))
             || ($showPageTitle && $page->translation->title)
             || ($showPageContent && $page->translation->content))
            <x-capell::content
                class="mb-5"
                :compact="true"
                :content="$block->translation->content ?? ($showPageContent ? $page->translation->content : null)"
                :content-type="$block->translation->content ? $block->type->content_structure : ($showPageContent ? $page->type->content_structure : null)"
                :divider="$block->getMeta('content_divider')"
                :muted="in_array($containerKey, $theme->secondary_containers)"
                :text-align="$block->getMeta('align')"
                :title="$block->translation->title ?? ($showPageTitle ? $page->translation->title : null)"
                :heading-style="$block->getMeta('heading_style')"
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
                        class="block-navigation-list"
                    >
                        @foreach ($chunk as $item)
                            <x-dynamic-component
                                :component="$item instanceof NavigationItemRenderData ? ($item->componentItem ?: 'capell::list.item') : (! empty($item->data['component_item']) ? $item->data['component_item'] : 'capell::list.item')"
                                class="block-navigation-item"
                                :$item
                            />
                        @endforeach
                    </x-dynamic-component>
                @endforeach
            </div>
        @else
            <x-dynamic-component
                :component="$listComponent"
                class="block-navigation-list block-navigation-lit-children text-sm"
            >
                @foreach ($items as $item)
                    <x-dynamic-component
                        :component="$item instanceof NavigationItemRenderData ? ($item->componentItem ?: 'capell::list.item') : (! empty($item->data['component_item']) ? $item->data['component_item'] : 'capell::list.item')"
                        :$item
                        class="block-navigation-item block-navigation-child-item"
                    />
                @endforeach
            </x-dynamic-component>
        @endif
    </x-capell-foundation-theme::block.wrapper>
@endif
