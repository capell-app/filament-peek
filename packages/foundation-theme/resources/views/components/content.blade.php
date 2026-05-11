@php
    use Capell\Core\Enums\ContentStructure;
    use Capell\Frontend\Actions\GetPageVariablesAction;
    use Capell\Frontend\Actions\RenderHtmlContentAction;
    use Capell\Frontend\Facades\Frontend;

    $page = Frontend::page();
    $language = Frontend::language();
    $site = Frontend::site();
    $layout = Frontend::layout();
    $theme = Frontend::theme();
@endphp

@props([
    'align' => '',
    'contentType' => ContentStructure::Html,
    'color' => '',
    'compact' => false,
    'content' => '',
    'divider' => null,
    'headingSize' => null,
    'headingTag' => null,
    'headingStyle' => null,
    'headingWeight' => 'normal',
    'headingBalance' => true,
    'image' => null,
    'muted' => null,
    'size' => '',
    'textAlign' => 'left',
    'title' => '',
    'width' => 'full',
])

@php
    if (! $headingSize && ! $headingTag) {
        $headingSize = $muted ? $headingTag = 'h4' : $headingTag = 'h3';
    }

    if (! $headingTag) {
        $headingTag = $headingSize;
    }

    if (! $muted && $headingStyle === 'secondary') {
        $muted = true;
    }

    if (is_string($content)) {
        $content = __($content, GetPageVariablesAction::run($page));
    }

    $title = __($title, GetPageVariablesAction::run($page));
@endphp

<div
    {{
        $attributes->class([
            'content-component prose prose-h1:font-bold [&>:first-child]:mt-0 [&>:last-child]:mb-0',
            'prose-invert' => $color === 'light' && $theme->withDarkMode,
            'dark:prose-invert' => $color !== 'light' && $theme->withDarkMode,
            'prose-muted' => $color === 'muted' || (! $color && $muted),
            'max-w-none' => $width === 'full',
            'mx-auto' => $align === 'center' || (! $align && $textAlign === 'center'),
            'prose-lg md:prose-xl lg:prose-2xl xl:prose-4xl' => $size === 'lg',
            'prose-sm' => $size === 'sm',
            'prose-compact' => $compact,
            'prose-headings:text-balance' => $headingBalance,
            'prose-headings:font-medium' => $headingWeight === 'medium',
            'prose-headings:font-normal' => $headingWeight === 'normal',
            'text-left' => $textAlign === 'left',
            'text-right' => $textAlign === 'right',
            'text-center' => $textAlign === 'center',
            $textAlign => ! in_array($textAlign, ['left', 'right', 'center'], true),
        ])
    }}
>
    @if ($image)
        {{-- format-ignore-start --}}
        <x-capell::media
                :media="$image"
                fit="crop"
                :width="360"
                data-group="gallery"
                :data-title="$image->name"
                :data-lightbox="$image->getFullUrl()"
                role="button"
                tabindex="0"
                aria-label="{{ __('capell-frontend::generic.open_image') }}: {{ $title }}"
                :alt="$title"
                @class([
                    'h-auto object-cover object-center lightbox cursor-pointer md:float-right md:max-w-[40%] md:ml-10 md:mt-0',
                    'rounded' => (bool) $theme->getMeta('rounded_images'),
                ])
                loading="lazy"
        />
        {{-- format-ignore-end --}}
    @endif

    @if ($divider === 'above_heading' && $title)
        <div
            aria-hidden="true"
            class="not-prose mb-4 border-t"
            style="border-color: var(--color-divider)"
        ></div>
    @endif

    @if ($title)
        {{-- format-ignore-start --}}
        <{{ $headingTag }}
            @class([
                '2xl:text-2xl font-medium mb-4 not-prose text-balance text-lg text-secondary xl:text-xl',
                'text-secondary' => $headingStyle === 'secondary',
                'text-4xl' => $headingSize === 'h1',
                'text-3xl' => $headingSize === 'h2',
                'text-2xl' => $headingSize === 'h3',
                'text-xl' => $headingSize === 'h4',
                'text-lg' => $headingSize === 'h5',
                'text-base' => $headingSize === 'h6',
                'font-medium' => $headingWeight === 'medium',
                'font-normal' => $headingWeight !== 'medium',
                'text-balance' => $headingBalance,
            ])
        >
            {{ $title }}
        </{{ $headingTag }}>
        {{-- format-ignore-end --}}
    @endif

    @if ($divider === 'below_heading' && $title)
        <div
            aria-hidden="true"
            class="not-prose mb-4 border-t"
            style="border-color: var(--color-divider)"
        ></div>
    @endif

    @if ($contentType === ContentStructure::Blocks)
        <x-capell::blocks
            :blocks="$content"
            :layout="$layout"
            :page="$page"
        />
    @else
        {!! RenderHtmlContentAction::run($content, ['page' => $page, 'site' => $site]) !!}
    @endif

    {{ $slot }}

    @if ($divider === 'below_content')
        <div
            aria-hidden="true"
            class="not-prose mt-4 border-t"
            style="border-color: var(--color-divider)"
        ></div>
    @endif
</div>
