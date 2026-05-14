@php
    use Capell\LayoutBuilder\Enums\ActionLinkEnum;
    use Capell\Core\Models\PageUrl;
    use Capell\Frontend\Facades\Frontend;
    use Capell\Frontend\Support\Loader\PageLoader;
    use Capell\Frontend\Support\Loader\SiteLoader;

    $page = Frontend::page();
    $language = Frontend::language();
    $site = Frontend::site();
    $theme = Frontend::theme();
@endphp

@props([
    'align' => 'start',
    'actions' => '',
    'actionItemClass' => '',
    'color' => 'light',
    'buttonSize' => 'lg',
    'buttonWeight' => 'bold',
    'buttonOutline' => null,
    'buttonColor' => 'primary',
])
<div
    {{
        $attributes->class([
            'actions flex flex-wrap gap-2 lg:gap-x-4',
            'justify-center' => $align === 'center',
            'justify-start' => $align === 'start' || $align === 'left',
            'justify-end' => $align === 'end' || $align === 'right',
        ])
    }}
>
    {{ $slot }}
    @foreach ($actions as $action)
        {{-- format-ignore-start --}}
        @php
            $url = $action['url'] ?? '';
            $wireNavigation = false;
            $rawType = $action['type'] ?? '';

            if ($rawType === 'public_action' && \Illuminate\Support\Facades\Route::has('capell-public-actions.submit')) {
                $publicActionKey = is_string($action['public_action_key'] ?? null) ? $action['public_action_key'] : null;
                $label = $action['label'] ?? '';
                $payload = array_filter([
                    'area' => $action['access_gate_area'] ?? null,
                    'requested_url' => url()->current(),
                    'redirect' => $action['redirect'] ?? null,
                    'source_type' => 'section_action',
                    'source_id' => $action['source_id'] ?? null,
                ], static fn (mixed $payloadValue): bool => $payloadValue !== null && $payloadValue !== '');
            }
        @endphp
        {{-- format-ignore-end --}}

        @if (($action['type'] ?? '') === 'public_action' && Route::has('capell-public-actions.submit'))
            <form
                method="post"
                action="{{ route('capell-public-actions.submit', ['action' => $publicActionKey]) }}"
                class="inline-flex"
            >
                @csrf
                @foreach ($payload as $payloadKey => $payloadValue)
                    @if (is_string($payloadKey) && is_scalar($payloadValue))
                        <input
                            type="hidden"
                            name="{{ $payloadKey }}"
                            value="{{ (string) $payloadValue }}"
                        />
                    @endif
                @endforeach

                <button
                    type="submit"
                    class="{{ 'action-item rounded-full px-3.5 py-2 text-xs font-semibold transition sm:px-5 sm:py-3 sm:text-sm ' . (($action['color'] ?? $buttonColor) === 'secondary' ? 'border border-slate-300 text-slate-800 hover:border-slate-950 dark:border-white/15 dark:text-slate-200 dark:hover:border-white' : 'bg-[var(--theme-accent)] text-slate-950 hover:bg-white') . ' ' . ($actionItemClass ?? '') }}"
                >
                    {{ $label }}
                </button>
            </form>
            @continue
        @endif

        @if (($action['type'] ?? '') === 'public_action')
            @continue
        @endif

        {{-- format-ignore-start --}}
        @php

            $type = ActionLinkEnum::tryFrom($rawType);

            switch ($type) {
                case ActionLinkEnum::Link:
                    $url = $action['url'] ?? '';
                    break;
                case ActionLinkEnum::Page:
                    if (
                        blank($action['site_id'] ?? null)
                        || blank($action['pageable_type'] ?? null)
                        || blank($action['pageable_id'] ?? null)
                    ) {
                        continue 2;
                    }

                    $targetSite = (int) $action['site_id'] === (int) $site->id
                        ? $site
                        : SiteLoader::getSites()->firstWhere('id', $action['site_id']);

                    if (! $targetSite instanceof \Capell\Core\Models\Site) {
                        continue 2;
                    }

                    $pageUrl = PageLoader::getUrlById(
                        pageType: $action['pageable_type'],
                        pageId: (int) $action['pageable_id'],
                        site: $targetSite,
                        language: $language,
                    );

                    if (! $pageUrl instanceof PageUrl) {
                        continue 2;
                    }

                    $url = $pageUrl->full_url;
                    break;
            }

            throw_unless($url, InvalidArgumentException::class, 'Action URL is missing.');

            $label = $action['label'] ?? $pageUrl->translation->link_text ?? '';

            $wireNavigation = true;
        @endphp
        {{-- format-ignore-end --}}

        <x-capell::button
            :$url
            :target="$action['target'] ?? ''"
            :color="$action['color'] ?? $buttonColor"
            :color="$color"
            :icon="$action['icon'] ?? ''"
            :outline="$buttonOutline === false"
            :size="$buttonSize"
            :weight="$buttonWeight"
            :wire-navigation="$wireNavigation"
            :class="'action-item' . ' ' . ($actionItemClass ?? '')"
        >
            @if ($action['hide_label'] ?? false)
                <span class="sr-only">
                    {{ $label }}
                </span>
            @else
                {{ $label }}
            @endif
        </x-capell::button>
    @endforeach
</div>
