@props([
    'align' => $element->getMeta('align'),
    'title' => $element->translation?->title,
    'content' => $element->translation?->content,
    'container',
    'loop',
    'containerKey',
    'containerWidth' => null,
    'element',
])

<x-capell-foundation-theme::element.wrapper
    class="element-snippet"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <x-capell::content
        class="element-content"
        :compact="true"
        :content="$content"
        :content-type="$element->type->content_structure"
        :divider="$element->getMeta('content_divider')"
        :heading-size="$element->getMeta('heading_size', 'h3')"
        :title="$title"
        :text-align="$align"
    />
</x-capell-foundation-theme::element.wrapper>
