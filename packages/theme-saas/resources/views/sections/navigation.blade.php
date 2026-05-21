<nav
    class="theme-navigation sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur"
    aria-label="{{ __('capell-theme-saas::generic.main_navigation') }}"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <a href="/" class="font-bold text-[var(--theme-primary)]">
            {{ $section->brandName }}
        </a>
        <div
            class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex"
        >
            @foreach ($section->items as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="hover:text-[var(--theme-primary)]"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
        <details class="relative md:hidden">
            <summary
                class="cursor-pointer list-none rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 marker:hidden"
            >
                {{ __('capell-theme-saas::generic.menu') }}
            </summary>
            <div
                class="absolute right-0 z-30 mt-3 grid min-w-48 gap-3 rounded-lg border border-slate-200 bg-white p-4 text-sm font-medium text-slate-600 shadow-xl"
            >
                @foreach ($section->items as $item)
                    <a
                        href="{{ $item['url'] }}"
                        class="hover:text-[var(--theme-primary)]"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </details>
        @if ($section->ctaLabel && $section->ctaUrl)
            <a
                href="{{ $section->ctaUrl }}"
                class="rounded-lg bg-[var(--theme-primary)] px-4 py-2 text-sm font-semibold text-white shadow-sm"
            >
                {{ $section->ctaLabel }}
            </a>
        @endif
    </div>
</nav>
<span id="main-content" tabindex="-1"></span>
