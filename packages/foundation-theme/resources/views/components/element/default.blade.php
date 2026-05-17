<?php
use Capell\Frontend\Facades\Frontend;

$theme = Frontend::theme();

?>

@props([
    'align' => $element->getMeta('align'),
    'headingSize' => $element->getMeta('heading_size', 'h2'),
    'size' => $element->getMeta('size'),
    'style' => $element->getMeta('style', 'row'),
    'reverseOrder' => $element->getMeta('reverse_order'),
    'title' => $element->translation?->title,
    'content' => $element->translation?->content,
    'container',
    'loop',
    'containerKey',
    'containerWidth' => null,
    'element',
])

<x-capell-foundation-theme::element.wrapper
    class="element-default"
    :container-class="
        'flex flex-col gap-x-5 gap-y-3 lg:gap-x-10 '
        . (match ($style) {
            'row' => ($reverseOrder ? 'md:flex-row-reverse' : 'md:flex-row'),
            default => null,
        })
    "
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <div
        @class([
            '@container flex-1',
            'my-auto py-4' => $element->image,
        ])
    >
        @if ($content || $title)
            <x-capell::content
                class="element-content mb-2"
                :compact="true"
                :content="$content"
                :content-type="$element->type->content_structure"
                :divider="$element->getMeta('content_divider')"
                :heading-size="$headingSize"
                :muted="in_array($containerKey, $theme->secondary_containers)"
                :heading-style="$element->getMeta('heading_style')"
                :title="$title"
                :text-align="$align"
            />
        @endif

        @if ($element->getMeta('actions'))
            <x-capell::actions
                class="mt-4"
                :actions="$element->getMeta('actions')"
                :align="$align"
            />
        @endif
    </div>

    @if ($element->image)
        <div
            @class([
                match ($style) {
                    'row' => 'flex-1 lg:max-w-[40%]',
                    default => null,
                },
            ])
        >
            <x-capell::media
                :media="$element->image"
                class="h-full w-full object-cover"
            />
        </div>
    @endif
</x-capell-foundation-theme::element.wrapper>
