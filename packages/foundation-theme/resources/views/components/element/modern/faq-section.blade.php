@props([
    'container',
    'containerKey',
    'containerWidth' => null,
    'loop',
    'element',
])

@php
    $categories = $element->assets
        ->map(fn ($wa) => $wa->asset->getMeta('category'))
        ->filter()
        ->unique()
        ->values()
        ->toArray();

    $hasCategories = count($categories) > 0;
@endphp

<style>
    @keyframes faqFadeIn {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<x-capell-foundation-theme::element.wrapper
    class="capell-modern-faq-section element-ap-faq-section"
    :$container
    :$containerKey
    :$containerWidth
    :index="$loop->index"
    :$element
>
    <section class="px-6 py-12 md:px-12 md:py-16">
        @if ($element->translation)
            <div class="mx-auto mb-12 max-w-2xl text-center">
                @if ($element->translation->title)
                    <h2
                        class="text-3xl font-bold tracking-tight text-gray-900 md:text-4xl"
                    >
                        {{ $element->translation->title }}
                    </h2>
                @endif

                @if ($element->translation->content)
                    <p class="mt-3 text-lg text-gray-500">
                        {{ strip_tags($element->translation->content) }}
                    </p>
                @endif
            </div>
        @endif

        @if ($hasCategories)
            <div
                class="mx-auto mb-8 flex max-w-3xl flex-wrap justify-center gap-2"
            >
                <button
                    class="faq-category-tab rounded-full bg-stone-800 px-4 py-2 text-sm font-semibold text-white transition-all"
                    data-category="all"
                    onclick="filterFaqCategory(this, 'all')"
                >
                    All
                </button>

                @foreach ($categories as $category)
                    <button
                        class="faq-category-tab rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-gray-600 transition-all hover:border-stone-400 hover:text-stone-800"
                        data-category="{{ $category }}"
                        onclick="filterFaqCategory(this, '{{ $category }}')"
                    >
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="faq-container mx-auto max-w-3xl space-y-3">
            @forelse ($element->assets as $elementAsset)
                <details
                    class="faq-item group rounded-xl border border-stone-200 bg-white"
                    data-category="{{ $elementAsset->asset->getMeta('category', 'uncategorized') }}"
                >
                    <summary
                        class="flex cursor-pointer select-none items-center justify-between p-5 text-base font-semibold text-gray-900"
                    >
                        <span>
                            {{ $elementAsset->asset->translation?->title }}
                        </span>
                        <span
                            class="ml-4 flex-shrink-0 text-xl text-stone-500 transition-transform group-open:rotate-45"
                        >
                            +
                        </span>
                    </summary>

                    @if ($elementAsset->asset->translation?->content)
                        <div
                            class="border-t border-stone-100 px-5 pb-5 pt-4 leading-relaxed text-stone-600"
                        >
                            {{ strip_tags($elementAsset->asset->translation->content) }}
                        </div>
                    @endif
                </details>
            @empty
                <div class="py-12 text-center">
                    <p class="text-gray-500">No FAQs configured</p>
                </div>
            @endforelse
        </div>
    </section>
</x-capell-foundation-theme::element.wrapper>

<script>
    function filterFaqCategory(button, category) {
        document.querySelectorAll('.faq-category-tab').forEach((tab) => {
            const isActive = tab.getAttribute('data-category') === category
            tab.className = isActive
                ? 'faq-category-tab rounded-full bg-stone-800 px-4 py-2 text-sm font-semibold text-white transition-all'
                : 'faq-category-tab rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-gray-600 transition-all hover:border-stone-400 hover:text-stone-800'
        })

        document.querySelectorAll('.faq-item').forEach((item) => {
            const matches =
                category === 'all' ||
                item.getAttribute('data-category') === category
            item.style.display = matches ? 'block' : 'none'
            if (matches) item.style.animation = 'faqFadeIn 0.25s ease-out'
        })
    }
</script>
