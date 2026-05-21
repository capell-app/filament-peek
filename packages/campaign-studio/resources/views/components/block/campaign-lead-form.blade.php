@props([
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'block',
])

<x-capell-foundation-theme::block.wrapper
    class="capell-block-campaign-lead-form block-campaign-lead-form"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$block
>
    <section
        class="campaign-lead-form px-6 py-12"
        data-campaign-goal="{{ $block->getMeta('goal_key') }}"
        data-campaign-location="lead-form"
    >
        @if ($block->translation?->title)
            <h2 class="mb-4 text-3xl font-bold">
                {{ $block->translation->title }}
            </h2>
        @endif

        @if ($block->translation?->content)
            <div class="mb-6">{!! $block->translation->content !!}</div>
        @endif

        @if ($block->getMeta('form_handle'))
            <livewire:capell-form-builder.form
                :handle="$block->getMeta('form_handle')"
            />
        @endif
    </section>
</x-capell-foundation-theme::block.wrapper>
