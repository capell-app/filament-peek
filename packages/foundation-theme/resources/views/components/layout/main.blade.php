@props([
    'layout',
    'containerClass' => null,
    'mainClass' => null,
    'mainContainerClass' => null,
    'pageSlot' => null,
    'page',
    'theme' => [],
])
@php
    use Capell\Core\Actions\ColorConverterAction;
    use Capell\Core\Contracts\Pageable;
    use Capell\Frontend\Facades\Frontend;
    use Capell\Frontend\Support\Loader\PageLoader;
    use Capell\LayoutBuilder\Facades\CapellLayout;

    $themeModel = Frontend::theme();
    $language = Frontend::language();
    $site = Frontend::site();
    $previousPage = (bool) $page->getMeta('with_next_prev')
        ? PageLoader::getPreviousPage($page, $site, $language)
        : null;
    $nextPage = (bool) $page->getMeta('with_next_prev')
        ? PageLoader::getNextPage($page, $site, $language)
        : null;
    $finalCta = $page->getMeta('final_cta');
@endphp

<style>
    :root {
        --bg-color-main: {{ ColorConverterAction::run($themeModel->getMeta('main_background_color', '#f8fafc')) }};
    }

    .dark:root {
        --bg-color-main: {{ ColorConverterAction::run($themeModel->getMeta('main_dark_background_color', '#111827')) }};
    }
</style>

<main
    id="main"
    @class([
        'relative z-0 flex min-h-full flex-1 flex-col overflow-x-hidden bg-[var(--bg-color-main)] lg:!min-h-0',
        $theme['meta']['main_class'] ?? '',
        $mainClass ?? '',
    ])
>
    {{-- format-ignore-start --}}
    <div
        @class([
            'grow',
            $mainContainerClass => (bool) $mainContainerClass,
        ])
    >
        @php
            $previousColspan = null;
            $slotRendered = false;
        @endphp

        @if ($layout->containers)
            @foreach ($layout->containers as $containerKey => $container)
                @php
                    $layoutModules = collect($container['elements'] ?? [])
                        ->map(fn (array $elementData): ?\Capell\LayoutBuilder\Models\Element => CapellLayout::getContainerElement(
                            (string) $containerKey,
                            (string) ($elementData['element_key'] ?? ''),
                            (int) ($elementData['occurrence'] ?? 1),
                        ))
                        ->filter();

                    if ($layoutModules->isEmpty()) {
                        continue;
                    }

                    $hasSlotWidget = ! $slotRendered && $layoutModules->contains(
                        fn (\Capell\LayoutBuilder\Models\Element $layoutModule): bool => ($layoutModule->meta['type'] ?? null) === 'slot'
                            || ($layoutModule->relationLoaded('type') && $layoutModule->type?->getMeta('type') === 'slot'),
                    );

                    $colspan = (int) ($container['meta']['colspan'] ?? 12);

                    $columnStart = (int) ($container['meta']['column_start'] ?? 0);

                    $htmlClass = $container['meta']['html_class'] ?? '';

                    if ($containerClass) {
                        if (is_string($containerClass)) {
                            $htmlClass .= ' ' . $containerClass;
                        } elseif (! empty($containerClass[$containerKey])) {
                            $htmlClass .= ' ' . $containerClass[$containerKey];
                        }
                    }
                @endphp
                <x-capell-layout-builder::layout.container
                    :$container
                    :$containerKey
                    :$layout
                    :containerIndex="$loop->index"
                    :colspan="$colspan"
                    :column-start="$columnStart"
                    :htmlClass="$htmlClass"
                    :pageSlot="$hasSlotWidget ? $pageSlot : null"
                    :previousColspan="$previousColspan"
                />

                @php
                    if ($hasSlotWidget && $pageSlot) {
                        $slotRendered = true;
                    }
                @endphp

                @php
                    $previousColspan += $colspan;
                    if ($columnStart) {
                        $previousColspan += $columnStart - 1;
                    }
                    $previousColspan = $previousColspan >= 12 ? 0 : $previousColspan;
                @endphp
            @endforeach
        @endif

        @if ($previousColspan && $previousColspan !== 12)
            </div>
        </div>
        @endif

        @if ($pageSlot && ! $slotRendered)
            {{ $pageSlot }}
            @php
                $slotRendered = true;
            @endphp
        @endif

        @if ($previousPage instanceof Pageable || $nextPage instanceof Pageable)
            <div class="capell-neighbor-links-mobile px-6 pb-12">
                <div class="neighbor-links">
                    @if ($previousPage)
                        <x-capell::page.neighbor-link
                            :neighbor-page="$previousPage"
                            neighbor="previous"
                        />
                    @endif

                    @if ($nextPage)
                        <x-capell::page.neighbor-link
                            :neighbor-page="$nextPage"
                            neighbor="next"
                        />
                    @endif
                </div>
            </div>
        @endif

        @if (is_array($finalCta) && filled($finalCta['title'] ?? null))
            <section class="capell-final-cta mx-auto mb-12 mt-4 max-w-7xl px-6">
                <div class="capell-final-cta-panel">
                    <div>
                        @if (filled($finalCta['kicker'] ?? null))
                            <p class="capell-section-kicker">
                                {{ $finalCta['kicker'] }}
                            </p>
                        @endif

                        <h2>{{ $finalCta['title'] }}</h2>

                        @if (filled($finalCta['summary'] ?? null))
                            <p>{{ $finalCta['summary'] }}</p>
                        @endif
                    </div>

                    @if (filled($finalCta['url'] ?? null) && filled($finalCta['label'] ?? null))
                        <a
                            href="{{ $finalCta['url'] }}"
                            class="capell-final-cta-link"
                            @wireNavigate
                        >
                            {{ $finalCta['label'] }}
                        </a>
                    @endif
                </div>
            </section>
        @endif
    </div>
    {{-- format-ignore-end --}}
</main>
