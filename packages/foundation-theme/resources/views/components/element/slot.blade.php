@props(['pageSlot', 'container' => null, 'containerKey' => null, 'containerWidth' => null, 'loop' => null, 'element' => null])

<x-capell-foundation-theme::element.wrapper
    class="capell-element-slot element-page-slot"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop?->index ?? 0"
    :$element
>
    <div>
        {{ $pageSlot }}
    </div>
</x-capell-foundation-theme::element.wrapper>
