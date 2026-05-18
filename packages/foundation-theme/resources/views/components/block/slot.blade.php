@props(['pageSlot', 'container' => null, 'containerKey' => null, 'containerWidth' => null, 'loop' => null, 'block' => null])

<x-capell-foundation-theme::block.wrapper
    class="capell-block-slot block-page-slot"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop?->index ?? 0"
    :$block
>
    <div>
        {{ $pageSlot }}
    </div>
</x-capell-foundation-theme::block.wrapper>
