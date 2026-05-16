@php
    use Capell\Core\Contracts\Pageable;
    use Capell\Core\Enums\ContentStructure;
    use Capell\FoundationTheme\View\Components\Widget\Page\Children as PageChildrenComponent;
    use Capell\FoundationTheme\View\Components\Widget\Page\Content as PageContentComponent;
    use Capell\FoundationTheme\View\Components\Widget\Page\Latest as PageLatestComponent;
    use Capell\FoundationTheme\View\Components\Widget\Page\Siblings as PageSiblingsComponent;
    use Capell\FoundationTheme\View\Components\Widget\Slot as SlotComponent;
    use Capell\Frontend\Facades\Frontend;
    use Capell\Frontend\Support\Loader\PageLoader;
@endphp

@props([
    'component',
    'container',
    'containerColspan' => null,
    'containerKey',
    'containerIndex',
    'containerWidth' => null,
    'loop',
    'occurrence' => $widgetData['occurrence'] ?? 1,
    'pageSlot' => null,
    'type',
    'widget',
    'widgetIndex',
    'widgetData',
])

@if ($type === 'blade')
    @if ($component === 'capell::widget.page.content')
        @php
            $page = Frontend::page();
            $layout = Frontend::layout();
            $language = Frontend::language();
            $site = Frontend::site();
            $theme = Frontend::theme();
            $pageContents = (array) $widget->getMeta('page_content', ['title', 'content']);
            $headingTag = $widget->getMeta('heading_tag');
            $headingSize = $widget->getMeta('heading_size', 'h1');
            $hasPrimaryHeading = (bool) Frontend::getFrontendData('has_primary_heading');
            $hasContent = in_array('content', $pageContents, true) && ! empty($page->translation->content);
            $hasTitle = in_array('title', $pageContents, true) && ! (empty($widgetData['meta']['show_page_title']) && $hasPrimaryHeading);
            $previousPage = (bool) $page->getMeta('with_next_prev')
                ? PageLoader::getPreviousPage($page, $site, $language)
                : null;
            $nextPage = (bool) $page->getMeta('with_next_prev')
                ? PageLoader::getNextPage($page, $site, $language)
                : null;

            if (! $headingTag) {
                $headingTag = $hasPrimaryHeading ? 'h2' : 'h1';
            }
        @endphp

        @if ($hasContent || $hasTitle)
            <x-capell-layout-builder::widget.wrapper
                :$container
                :$containerKey
                :$containerWidth
                :index="$loop->index"
                :$widget
                class="widget-page-content"
                tag="article"
            >
                @if (in_array('content', $pageContents, true))
                    @if ($page->type->content_structure === ContentStructure::Blocks)
                        @if ($hasTitle)
                            <{{ $headingTag }}
                                class="text-{{ $headingSize }} mb-6"
                            >
                                {{ $page->translation->title }}
                            </{{ $headingTag }}>
                        @endif

                        <x-capell::blocks
                            :blocks="$page->translation->content"
                            :$layout
                            :$containerKey
                            :$page
                        />
                    @else
                        <x-capell::content
                            :content="$page->translation->content"
                            :content-type="$page->type->content_structure"
                            :divider="$widget->getMeta('content_divider')"
                            :heading-size="$headingSize"
                            :heading-tag="$headingTag"
                            :muted="in_array($containerKey, $theme->secondary_containers)"
                            :image="$page->image"
                            :text-align="$widget->getMeta('align')"
                            :title="$hasTitle ? $page->translation->title : null"
                        />
                    @endif
                @endif

                @if ($previousPage instanceof Pageable || $nextPage instanceof Pageable)
                    <div class="clear-both">
                        <div
                            class="capell-neighbor-links-desktop neighbor-links mt-10 flex divide-y divide-gray-100 border-t border-gray-100 pt-6 md:divide-x md:divide-y-0"
                        >
                            @if ($previousPage)
                                <x-capell::page.neighbor-link
                                    :neighbor-page="$previousPage"
                                    neighbor="previous"
                                />
                            @endif

                            @if ($nextPage)
                                <x-capell::page.neighbor-link
                                    :neighbor-page="$nextPage"
                                    neighbor="next"
                                    class="ml-auto"
                                />
                            @endif
                        </div>
                    </div>
                @endif
            </x-capell-layout-builder::widget.wrapper>
        @endif
    @else
        @php
            $pageWidgetComponent = match ($component) {
                'capell::widget.page.children' => PageChildrenComponent::class,
                'capell::widget.page.content' => PageContentComponent::class,
                'capell::widget.page.latest' => PageLatestComponent::class,
                'capell::widget.page.siblings' => PageSiblingsComponent::class,
                'capell::widget.slot' => SlotComponent::class,
                default => null,
            };
        @endphp

        @if ($pageWidgetComponent !== null)
            @php
                $pageWidget = new $pageWidgetComponent(
                    container: $container,
                    containerKey: $containerKey,
                    widgetIndex: $widgetIndex,
                    loop: $loop,
                    widget: $widget,
                    widgetData: $widgetData,
                    pageSlot: $pageSlot,
                );
            @endphp

            {!! $pageWidget->render() !!}
        @else
            <x-dynamic-component
                :component="$component"
                :$container
                :$containerColspan
                :$containerKey
                :$containerIndex
                :$containerWidth
                :element="$widget"
                :elementData="$widgetData"
                :elementIndex="$widgetIndex"
                :$loop
                :$pageSlot
                :$occurrence
                :$widget
                :$widgetData
                :$widgetIndex
            />
        @endif
    @endif
@elseif ($type === 'livewire')
    @php
        $livewireWidgetData = [
            ...$widgetData,
            'element_key' => $widgetData['element_key'] ?? $widget->key,
        ];
    @endphp

    @livewire($component,
        [
            'container' => $container,
            'containerColspan' => $containerColspan,
            'containerKey' => $containerKey,
            'containerIndex' => $containerIndex,
            'containerWidth' => $containerWidth,
            'loop' => $loop,
            'pageSlot' => $pageSlot,
            'occurrence' => $occurrence,
            'widget' => $widget,
            'widgetData' => $livewireWidgetData,
            'widgetIndex' => $widgetIndex,
        ],
        key($containerKey . '-' . $widget->key . '-' . $occurrence))
@endif
