@php
    use Capell\Core\Contracts\Pageable;
    use Capell\Core\Enums\ContentStructure;
@endphp

@props([
    'container',
    'containerKey',
    'containerWidth' => null,
    'hasPrimaryHeading' => false,
    'headingTag' => $widget->getMeta('heading_tag'),
    'headingSize' => $widget->getMeta('heading_size', 'h1'),
    'layout' => null,
    'loop',
    'pageRecord' => null,
    'pageContents' => ['title', 'content'],
    'size' => $widget->getMeta('size', 'lg'),
    'site' => null,
    'theme' => null,
    'urlParams' => null,
    'widget',
    'widgetData',
])
@php
    $nextPage ??= null;
    $previousPage ??= null;
    $configuredPageContents = $widget->getMeta('page_content') ?: ($widgetData['meta']['page_content'] ?? null);
    $pageContents = is_array($configuredPageContents) ? $configuredPageContents : $pageContents;
    $pageContents = array_values(array_filter((array) $pageContents));
    $pageContents = $pageContents === [] ? ['title', 'content'] : $pageContents;
@endphp

{{-- format-ignore-start --}}
@php
    $page = $pageRecord;
    $pageTranslation = $page instanceof Pageable && method_exists($page, 'relationLoaded') && $page->relationLoaded('translation')
        ? $page->getRelation('translation')
        : null;
    $pageType = $page instanceof Pageable && method_exists($page, 'relationLoaded') && $page->relationLoaded('type')
        ? $page->getRelation('type')
        : null;
    $secondaryContainers = $theme?->secondary_containers ?? ['sidebar'];

    $hasContent = in_array('content', $pageContents, true) && ! empty($pageTranslation?->content) && $pageType !== null;
    $hasTitle = in_array('title', $pageContents, true) && ! (empty($widgetData['meta']['show_page_title']) && $hasPrimaryHeading);
    $hasNeighborLinks = $previousPage instanceof Pageable || $nextPage instanceof Pageable;
    $pageImage = $page instanceof Pageable && method_exists($page, 'relationLoaded') && $page->relationLoaded('image')
        ? $page->getRelation('image')
        : null;

    if (! $headingTag) {
        $headingTag = ($hasPrimaryHeading ? 'h2' : 'h1');
    }

@endphp
{{-- format-ignore-end --}}
@if ($hasContent || $hasTitle || $hasNeighborLinks)
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
            @if ($pageType?->content_structure === ContentStructure::Blocks)
                @if ($hasTitle)
                    <{{ $headingTag }} class="text-{{ $headingSize }} mb-6">
                        {{ $pageTranslation?->title }}
                    </{{ $headingTag }}>
                @endif

                <x-capell::blocks
                    :blocks="$pageTranslation?->content"
                    :$layout
                    :$containerKey
                    :$page
                />
            @else
                <x-capell::content
                    :content="$pageTranslation?->content"
                    :content-type="$pageType?->content_structure"
                    :divider="$widget->getMeta('content_divider')"
                    :heading-size="$headingSize"
                    :heading-tag="$headingTag"
                    :$layout
                    :muted="in_array($containerKey, $secondaryContainers)"
                    :image="$pageImage"
                    :page-record="$page"
                    :site="$site"
                    :text-align="$widget->getMeta('align')"
                    :theme="$theme"
                    :title="$hasTitle ? $pageTranslation?->title : null"
                    :url-params="$urlParams"
                />
            @endif
        @endif

        @if (! empty($widget->translation?->actions))
            <x-capell-layout-builder::actions
                class="mt-4"
                :actions="$widget->translation?->actions"
                button_color="primary"
            />
        @endif

        @if ($hasNeighborLinks)
            <div class="clear-both">
                <nav
                    class="neighbor-links mt-10 flex divide-y divide-gray-100 border-t border-gray-100 pt-6 md:divide-x md:divide-y-0"
                    aria-label="{{ __('capell-foundation-theme::generic.page_navigation') }}"
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
                </nav>
            </div>
        @endif
    </x-capell-layout-builder::widget.wrapper>
@endif
