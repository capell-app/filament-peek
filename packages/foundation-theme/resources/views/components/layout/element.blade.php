@php
    use Capell\FoundationTheme\View\Components\Element\Page\Children as PageChildrenComponent;
    use Capell\FoundationTheme\View\Components\Element\Page\Content as PageContentComponent;
    use Capell\FoundationTheme\View\Components\Element\Page\Latest as PageLatestComponent;
    use Capell\FoundationTheme\View\Components\Element\Page\Siblings as PageSiblingsComponent;
    use Capell\FoundationTheme\View\Components\Element\Slot as SlotComponent;
    use Capell\Frontend\Facades\Frontend;
    use Illuminate\Contracts\View\View as ViewContract;
    use Illuminate\Support\Facades\Crypt;
    use Livewire\Blaze\Blaze;
@endphp

@props([
    'component',
    'container',
    'containerColspan' => null,
    'containerKey',
    'containerIndex',
    'containerWidth' => null,
    'layout',
    'loop',
    'occurrence' => $elementData['occurrence'] ?? 1,
    'pageSlot' => null,
    'type',
    'element',
    'elementIndex',
    'elementData',
])

@if ($type === 'blade')
    @php
        $pageElementComponent = match ($component) {
            'capell::element.page.children' => PageChildrenComponent::class,
            'capell::element.page.content' => PageContentComponent::class,
            'capell::element.page.latest' => PageLatestComponent::class,
            'capell::element.page.siblings' => PageSiblingsComponent::class,
            'capell::element.slot' => SlotComponent::class,
            'capell::element.page.children' => PageChildrenComponent::class,
            'capell::element.page.content' => PageContentComponent::class,
            'capell::element.page.latest' => PageLatestComponent::class,
            'capell::element.page.siblings' => PageSiblingsComponent::class,
            'capell::element.slot' => SlotComponent::class,
            default => null,
        };
    @endphp

    @if ($pageElementComponent !== null)
        @php
            $pageElement = new $pageElementComponent(
                container: $container,
                containerKey: $containerKey,
                elementIndex: $elementIndex,
                loop: $loop,
                element: $element,
                elementData: $elementData,
                pageSlot: $pageSlot,
            );

            $pageElementOutput = $pageElement->render();

            if ($pageElementOutput instanceof ViewContract) {
                $wasBlazeEnabled = Blaze::isEnabled();
                Blaze::disable();

                try {
                    $pageElementOutput = $pageElementOutput->render();
                } finally {
                    if ($wasBlazeEnabled) {
                        Blaze::enable();
                    }
                }
            }
        @endphp

        {!! $pageElementOutput !!}
    @else
        <x-dynamic-component
            class="capell-foundation-theme-layout-element"
            :component="$component"
            :$container
            :$containerColspan
            :$containerKey
            :$containerIndex
            :$containerWidth
            :element="$element"
            :elementData="$elementData"
            :elementIndex="$elementIndex"
            :$loop
            :$pageSlot
            :$occurrence
            :$element
            :$elementData
            :$elementIndex
        />
    @endif
@elseif ($type === 'livewire')
    @php
        $elementReference = Crypt::encryptString(json_encode([
            'container_key' => $containerKey,
            'element_key' => $elementData['element_key'] ?? $element->key,
            'layout_id' => $layout?->getKey(),
            'language_id' => Frontend::language()?->getKey(),
            'occurrence' => $occurrence,
            'page_id' => Frontend::page()?->getKey(),
            'page_type' => Frontend::page()?->getMorphClass(),
            'site_id' => Frontend::site()?->getKey(),
            'element_data' => $elementData,
            'element_index' => $elementIndex,
        ], JSON_THROW_ON_ERROR));
    @endphp

    @livewire($component,
        [
            'elementReference' => $elementReference,
        ],
        key($containerKey . '-' . $element->key . '-' . $occurrence))
@endif
