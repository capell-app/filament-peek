{{--
    Modern Team Members Widget
    
    Props:
    - title (string): Section heading
    - members (array): Array of team member objects
    Each member: { name, role, avatar, bio, tags[], social[] }
    - columns (int): Number of columns (2,3,4) - Default: 3
    - customizable (bool): Show admin hints
--}}

@props([
    'title' => 'Our Team',
    'members' => [
        [
            'name' => 'Alex Morgan',
            'role' => 'Product Lead',
            'avatar' => '👨‍💻',
            'bio' => 'Creative designer with 5+ years experience',
            'tags' => ['Design', 'Leadership'],
            'social' => ['twitter' => 'https://twitter.com', 'linkedin' => 'https://linkedin.com'],
        ],
        [
            'name' => 'Emma Davis',
            'role' => 'Engineering Manager',
            'avatar' => '👩‍🔬',
            'bio' => 'Full-stack developer and architect',
            'tags' => ['Engineering', 'Architecture'],
            'social' => ['github' => 'https://github.com', 'linkedin' => 'https://linkedin.com'],
        ],
        [
            'name' => 'James Wilson',
            'role' => 'CEO & Co-founder',
            'avatar' => '🧑‍💼',
            'bio' => 'Serial entrepreneur and visionary',
            'tags' => ['Strategy', 'Leadership'],
            'social' => ['twitter' => 'https://twitter.com', 'linkedin' => 'https://linkedin.com'],
        ],
    ],
    'columns' => 3,
    'customizable' => true,
])

@php
    $gridClasses = [
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
    ];

    $gridClass = $gridClasses[$columns] ?? $gridClasses[3];
@endphp

<section class="mosaic-team-members px-6 py-12 md:px-12 md:py-16">
    {{-- Header --}}
    @if ($title)
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <h2
                class="mb-4 text-3xl font-bold md:text-4xl"
                style="
                    color: var(--mosaic-on-surface);
                    font-family: var(--mosaic-font-headline);
                "
            >
                {{ $title }}
            </h2>
        </div>
    @endif

    {{-- Team Grid --}}
    <div class="{{ $gridClass }} grid gap-6">
        @forelse ($members as $member)
            <div
                class="mosaic-card overflow-hidden text-center"
                style="background-color: var(--mosaic-surface-container)"
            >
                {{-- Avatar --}}
                @if (isset($member['avatar']))
                    <div
                        class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full text-5xl"
                        style="
                            background-color: var(
                                --mosaic-surface-container-high
                            );
                        "
                    >
                        {{ $member['avatar'] }}
                    </div>
                @endif

                {{-- Name --}}
                @if (isset($member['name']))
                    <h3
                        class="mb-1 text-lg font-bold"
                        style="color: var(--mosaic-on-surface)"
                    >
                        {{ $member['name'] }}
                    </h3>
                @endif

                {{-- Role --}}
                @if (isset($member['role']))
                    <p
                        class="mb-3 text-sm font-semibold"
                        style="
                            color: var(--mosaic-tertiary);
                            text-transform: uppercase;
                            letter-spacing: 0.05em;
                        "
                    >
                        {{ $member['role'] }}
                    </p>
                @endif

                {{-- Bio --}}
                @if (isset($member['bio']))
                    <p
                        class="mb-4 text-base leading-relaxed"
                        style="color: var(--mosaic-on-surface-variant)"
                    >
                        {{ $member['bio'] }}
                    </p>
                @endif

                {{-- Tags/Badges --}}
                @if (isset($member['tags']) && count($member['tags']) > 0)
                    <div class="mb-4 flex flex-wrap justify-center gap-2">
                        @foreach ($member['tags'] as $tag)
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                style="
                                    background-color: var(
                                        --mosaic-primary-container
                                    );
                                    color: var(--mosaic-on-primary-container);
                                "
                            >
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif

                {{-- Social Links --}}
                @if (isset($member['social']) && count($member['social']) > 0)
                    <div
                        class="flex justify-center gap-3 pt-4"
                        style="
                            border-top: 1px solid var(--mosaic-outline-variant);
                        "
                    >
                        @php
                            $socialIcons = [
                                'twitter' => '𝕏',
                                'linkedin' => 'in',
                                'github' => '🐙',
                                'website' => '🌐',
                                'email' => '✉',
                            ];
                        @endphp

                        @foreach ($member['social'] as $platform => $url)
                            @if ($url && isset($socialIcons[$platform]))
                                <a
                                    href="{{ $url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="{{ ucfirst($platform) }}"
                                    style="
                                        color: var(--mosaic-primary);
                                        text-decoration: none;
                                        font-size: 1rem;
                                        font-weight: 600;
                                        transition: opacity 0.2s;
                                    "
                                    onmouseover="this.style.opacity = '0.7'"
                                    onmouseout="this.style.opacity = '1'"
                                >
                                    {{ $socialIcons[$platform] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <p style="color: var(--mosaic-on-surface-variant)">
                    No team members configured
                </p>
            </div>
        @endforelse
    </div>

    {{-- Admin Hint --}}
    @if ($customizable && auth()->check())
        <div
            class="mt-12 max-w-full pt-8 text-center"
            style="
                border-top: 1px solid var(--mosaic-outline-variant);
                opacity: 0.6;
            "
        >
            <span class="mosaic-text-label text-xs">
                ✨ Customize: Add members, tags, social links, change layout
            </span>
        </div>
    @endif
</section>
