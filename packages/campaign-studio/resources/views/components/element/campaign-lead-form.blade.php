@props([
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'element',
])

<x-capell-layout-builder::element.wrapper
    class="capell-element-campaign-lead-form element-campaign-lead-form"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <section
        class="campaign-lead-form px-6 py-12"
        data-campaign-goal="{{ $element->getMeta('goal_key') }}"
        data-campaign-location="lead-form"
    >
        @if ($element->translation?->title)
            <h2 class="mb-4 text-3xl font-bold">
                {{ $element->translation->title }}
            </h2>
        @endif

        @if ($element->translation?->content)
            <div class="mb-6">{!! $element->translation->content !!}</div>
        @endif

        @if ($element->getMeta('form_handle'))
            <livewire:capell-form-builder.form
                :handle="$element->getMeta('form_handle')"
            />
        @endif
    </section>
</x-capell-layout-builder::element.wrapper>
