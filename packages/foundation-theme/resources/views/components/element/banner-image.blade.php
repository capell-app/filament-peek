@props([
    'backgroundColor' => $element->getMeta('background_color'),
    'container',
    'containerKey',
    'containerWidth' => null,
    'content' => $element->translation?->content,
    'headingSize' => $element->getMeta('heading_size', 'h2'),
    'loop',
    'reverseOrder' => $element->getMeta('reverse_order'),
    'rounded' => (bool) $element->getMeta('rounded_images'),
    'size' => $element->getMeta('size'),
    'title' => $element->translation?->title,
    'element',
])
{{-- format-ignore-start --}}
@php
    use Capell\Core\Enums\ContainerWidthEnum;use Capell\FoundationTheme\Actions\BuildBannerImageRenderDataAction;use Capell\Frontend\Facades\Frontend;

    $theme = Frontend::theme();

    /**
    * @var \Capell\LayoutBuilder\Models\Element $element
    */
    $renderData = BuildBannerImageRenderDataAction::run($element, $content, $title, $rounded, $reverseOrder);
    $backgroundImage = $renderData->backgroundImage;
    $actions = $renderData->actions;
    $hasContent = $renderData->hasContent;
    $imgRounded = $renderData->imageRoundedClass;
@endphp
{{-- format-ignore-end --}}

<x-capell-foundation-theme::element.wrapper
    class="capell-element-banner-image element-banner-image relative"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :background-color="$backgroundColor"
    :$element
    :container-width="ContainerWidthEnum::Full"
>
    @if ($backgroundImage)
        <div
            @class([
                'w-full',
                'md:w-1/2' => $hasContent,
                'md:absolute' => $hasContent,
                'md:inset-y-0' => $hasContent,
                'md:left-0' => $hasContent && $reverseOrder,
                'md:right-0' => $hasContent && ! $reverseOrder,
            ])
        >
            <x-capell::media
                :media="$backgroundImage"
                size="xxl"
                :rounded="false"
                :alt="$hasContent ? '' : null"
                :class="'h-auto w-full object-cover md:h-full' . $imgRounded"
                :aria-hidden="$hasContent ? 'true' : null"
            />
        </div>
    @endif

    @if ($hasContent)
        <div
            @class([
                'container',
                'z-10',
                'absolute inset-0 flex items-end' => $backgroundImage,
                'relative flex flex-col' => ! $backgroundImage,
                'md:relative md:flex md:flex-col md:items-center',
                'gap-y-6',
                'gap-x-6',
                'py-10',
                'md:flex-row-reverse' => $reverseOrder,
                'md:flex-row' => ! $reverseOrder,
            ])
        >
            <div
                @class([
                    'w-full',
                    'md:w-1/2' => $backgroundImage,
                    'md:pl-10' => $backgroundImage && $reverseOrder,
                    'md:pr-10' => $backgroundImage && ! $reverseOrder,
                ])
            >
                <div
                    @class([
                        'rounded p-6' => $backgroundImage && $hasContent,
                        'bg-white/90 shadow-sm backdrop-blur' => $backgroundImage && $hasContent,
                    ])
                >
                    @if ($content || $title)
                        <x-capell::content
                            class="mb-2"
                            :compact="true"
                            :content="$content"
                            :content-type="$element->type->content_structure"
                            :divider="$element->getMeta('content_divider')"
                            :heading-size="$headingSize"
                            :title="$title"
                            :text-align="$element->getMeta('align')"
                            :heading-style="$element->getMeta('heading_style')"
                        />
                    @endif

                    @if ($actions)
                        <x-capell::actions class="mt-4" :$actions />
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-capell-foundation-theme::element.wrapper>
