{{--
  Modern Image Gallery Widget

  Props:
    - title (string): Section heading
    - subtitle (string): Section description
    - images (array): Array of image objects { src, alt, caption }
    - columns (int): Number of columns (2,3,4)
    - layout (string): grid or masonry
    - customizable (bool): Show admin hints
--}}

@props([
    'title' => 'Our Work',
    'subtitle' => 'Showcasing our latest projects',
    'images' => [
        [
            'src' => '🖼️',
            'alt' => 'Project 1',
            'caption' => 'Modern Design',
        ],
        [
            'src' => '🏗️',
            'alt' => 'Project 2',
            'caption' => 'Building Solutions',
        ],
        [
            'src' => '🎨',
            'alt' => 'Project 3',
            'caption' => 'Creative Direction',
        ],
        [
            'src' => '📱',
            'alt' => 'Project 4',
            'caption' => 'Mobile First',
        ],
        [
            'src' => '🚀',
            'alt' => 'Project 5',
            'caption' => 'Launch Success',
        ],
        [
            'src' => '⭐',
            'alt' => 'Project 6',
            'caption' => 'Excellence',
        ],
    ],
    'columns' => 3,
    'layout' => 'grid',
    'customizable' => true,
])

<section class="mosaic-gallery py-12 md:py-16 px-6 md:px-12">
    {{-- Header --}}
    @if($title)
        <div class="mb-12 text-center max-w-2xl mx-auto">
            <h2
                class="text-3xl md:text-4xl font-bold mb-3"
                style="
                    color: var(--mosaic-on-surface);
                    font-family: var(--mosaic-font-headline);
                "
            >
                {{ $title }}
            </h2>
            @if($subtitle)
                <p
                    class="text-lg"
                    style="color: var(--mosaic-on-surface-variant);"
                >
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif

    {{-- Gallery Grid --}}
    <div
        class="
            grid
            {{ $columns === 2 ? 'grid-cols-1 md:grid-cols-2' : '' }}
            {{ $columns === 3 ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3' : '' }}
            {{ $columns === 4 ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4' : '' }}
            gap-6
            max-w-6xl mx-auto
        "
    >
        @forelse($images as $image)
            <div
                class="group relative overflow-hidden rounded-lg cursor-pointer"
                style="
                    background-color: var(--mosaic-surface-container);
                    aspect-ratio: 1;
                    transition: transform 0.3s ease;
                "
                @mouseenter="$el.style.transform = 'scale(1.05)'"
                @mouseleave="$el.style.transform = 'scale(1)'"
            >
                {{-- Image --}}
                <div
                    class="w-full h-full flex items-center justify-center text-6xl"
                    style="background-color: var(--mosaic-surface-container-high);"
                >
                    @if(str($image['src'] ?? '')->startsWith('http'))
                        <img
                            src="{{ $image['src'] }}"
                            alt="{{ $image['alt'] ?? 'Gallery image' }}"
                            class="w-full h-full object-cover"
                        />
                    @else
                        {{ $image['src'] ?? '🖼️' }}
                    @endif
                </div>

                {{-- Overlay --}}
                @if(isset($image['caption']))
                    <div
                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 flex items-end justify-start p-6 transition-all duration-300"
                    >
                        <p
                            class="text-white font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        >
                            {{ $image['caption'] }}
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <p style="color: var(--mosaic-on-surface-variant);">No images configured</p>
            </div>
        @endforelse
    </div>

    {{-- Admin Hint --}}
    @if($customizable && auth()->check())
        <div class="mt-12 pt-8 max-w-full text-center" style="border-top: 1px solid var(--mosaic-outline-variant); opacity: 0.6;">
            <span class="mosaic-text-label text-xs">
                ✨ Customize: Add images, change layout, columns, and captions
            </span>
        </div>
    @endif
</section>

<style scoped>
    .grid { display: grid; }
    .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

    .gap-6 { gap: 1.5rem; }

    .max-w-2xl { max-width: 42rem; }
    .max-w-6xl { max-width: 72rem; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .max-w-full { max-width: 100%; }

    .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
    .py-16 { padding-top: 4rem; padding-bottom: 4rem; }
    .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
    .px-12 { padding-left: 3rem; padding-right: 3rem; }
    .p-6 { padding: 1.5rem; }

    .mb-12 { margin-bottom: 3rem; }
    .mb-3 { margin-bottom: 0.75rem; }
    .mt-12 { margin-top: 3rem; }
    .pt-8 { padding-top: 2rem; }

    .text-center { text-align: center; }

    .text-3xl { font-size: 1.875rem; }
    .text-4xl { font-size: 2.25rem; }
    .text-lg { font-size: 1.125rem; }
    .text-6xl { font-size: 3.75rem; }
    .text-xs { font-size: 0.75rem; }

    .font-bold { font-weight: 700; }
    .font-semibold { font-weight: 600; }

    .group { position: relative; }
    .relative { position: relative; }
    .absolute { position: absolute; }

    .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }

    .w-full { width: 100%; }
    .h-full { height: 100%; }

    .flex { display: flex; }
    .items-center { align-items: center; }
    .items-end { align-items: flex-end; }
    .justify-center { justify-content: center; }
    .justify-start { justify-content: flex-start; }

    .overflow-hidden { overflow: hidden; }
    .rounded-lg { border-radius: 0.5rem; }

    .cursor-pointer { cursor: pointer; }

    .object-cover { object-fit: cover; }

    .bg-black { background-color: #000; }
    .bg-opacity-0 { opacity: 0; }
    .bg-opacity-60 { opacity: 0.6; }

    .opacity-0 { opacity: 0; }
    .opacity-100 { opacity: 1; }

    .transition-all { transition: all 0.3s ease; }
    .transition-opacity { transition: opacity 0.3s ease; }
    .duration-300 { transition-duration: 0.3s; }

    .group-hover\:bg-opacity-60:hover { opacity: 0.6; }
    .group-hover\:opacity-100:hover { opacity: 1; }

    .col-span-full { grid-column: 1 / -1; }

    @media (max-width: 768px) {
        .md\:text-4xl { font-size: 2.25rem; }
        .md\:py-16 { padding-top: 4rem; padding-bottom: 4rem; }
        .md\:px-12 { padding-left: 3rem; padding-right: 3rem; }
    }
</style>
