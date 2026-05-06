@php
    use Capell\Core\Enums\ContentStructure;
    use Capell\Frontend\Facades\Frontend;
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
    @if ($component === 'capell-layout-builder::widget.page.content')
        @php
            $page = Frontend::page();
            $layout = Frontend::layout();
            $theme = Frontend::theme();
            $pageContents = (array) $widget->getMeta('page_content', ['title', 'content']);
            $headingTag = $widget->getMeta('heading_tag');
            $headingSize = $widget->getMeta('heading_size', 'h1');
            $hasPrimaryHeading = (bool) Frontend::getFrontendData('has_primary_heading');
            $hasContent = in_array('content', $pageContents, true) && ! empty($page->translation->content);
            $hasTitle = in_array('title', $pageContents, true) && ! (empty($widgetData['meta']['show_page_title']) && $hasPrimaryHeading);

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
            </x-capell-layout-builder::widget.wrapper>
        @endif
    @else
        <x-dynamic-component
            :component="$component"
            :$container
            :$containerColspan
            :$containerKey
            :$containerIndex
            :$containerWidth
            :$loop
            :$pageSlot
            :$occurrence
            :$widget
            :$widgetData
            :$widgetIndex
        />
    @endif
@elseif ($type === 'livewire')
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
            'widgetData' => $widgetData,
            'widgetIndex' => $widgetIndex,
        ],
        key($containerKey . '-' . $widget->key . '-' . $occurrence))
@endif
