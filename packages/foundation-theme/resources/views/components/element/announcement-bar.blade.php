@props([
    'align' => $element->getMeta('align', 'center'),
    'title' => $element->translation?->title,
    'content' => $element->translation?->content,
    'container',
    'loop',
    'containerKey',
    'containerWidth' => null,
    'element',
])

<x-capell-foundation-theme::element.wrapper
    class="capell-element-announcement-bar element-announcement-bar"
    container-class="text-center"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <div class="border-current/15 rounded-md border px-4 py-3">
        <x-capell::content
            class="element-content"
            :compact="true"
            :content="$content"
            :content-type="$element->type->content_structure"
            :heading-size="$element->getMeta('heading_size', 'h3')"
            :title="$title"
            :text-align="$align"
        />

        @if ($element->getMeta('actions'))
            <x-capell::actions
                class="mt-3"
                :actions="$element->getMeta('actions')"
                :align="$align"
            />
        @endif
    </div>
</x-capell-foundation-theme::element.wrapper>
