@props([
    'align' => $block->getMeta('align'),
    'title' => $block->translation?->title,
    'content' => $block->translation?->content,
    'container',
    'loop',
    'containerKey',
    'containerWidth' => null,
    'block',
])

<x-capell-foundation-theme::block.wrapper
    class="capell-block-snippet block-snippet"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$block
>
    <x-capell::content
        class="block-content"
        :compact="true"
        :content="$content"
        :content-type="$block->type->content_structure"
        :divider="$block->getMeta('content_divider')"
        :heading-size="$block->getMeta('heading_size', 'h3')"
        :title="$title"
        :text-align="$align"
    />
</x-capell-foundation-theme::block.wrapper>
