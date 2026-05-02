@php
    $record = $getRecord();
    $pluginName = (string) ($record['name'] ?? '');
    $pluginInitials = collect(explode(' ', $pluginName))
        ->filter()
        ->take(2)
        ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');
    $constraints = array_filter([
        __('capell-extension-marketplace::marketplace.card.capell_constraint', ['constraint' => $record['capell_version_constraint'] ?? '']),
        __('capell-extension-marketplace::marketplace.card.laravel_constraint', ['constraint' => $record['laravel_version_constraint'] ?? '']),
        __('capell-extension-marketplace::marketplace.card.filament_constraint', ['constraint' => $record['filament_version_constraint'] ?? '']),
        __('capell-extension-marketplace::marketplace.card.livewire_constraint', ['constraint' => $record['livewire_version_constraint'] ?? '']),
    ], fn (string $constraint): bool => ! str_ends_with($constraint, ': '));
    $categoryLabels = is_array($record['category_labels'] ?? null) ? $record['category_labels'] : [];
    $capabilityLabels = is_array($record['capability_labels'] ?? null) ? $record['capability_labels'] : [];
    $compatibilityWarnings = is_array($record['compatibility_warnings'] ?? null) ? $record['compatibility_warnings'] : [];
@endphp

<article
    {{ $attributes->merge($getExtraAttributes())->class(['flex h-full min-h-72 flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-white/10 dark:bg-gray-900']) }}
>
    <div class="flex items-start gap-4">
        <div
            class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100 text-sm font-bold text-gray-500 ring-1 ring-gray-200 dark:bg-white/10 dark:text-gray-300 dark:ring-white/10"
        >
            @if (is_string($record['image_url'] ?? null) && $record['image_url'] !== '')
                <img
                    src="{{ $record['image_url'] }}"
                    alt="{{ __('capell-extension-marketplace::marketplace.card.image_alt', ['name' => $pluginName]) }}"
                    class="size-full object-cover"
                    loading="lazy"
                />
            @else
                {{ $pluginInitials !== '' ? $pluginInitials : __('capell-extension-marketplace::marketplace.card.image_fallback') }}
            @endif
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div class="min-w-0">
                    <h3
                        class="truncate text-base font-semibold text-gray-950 dark:text-white"
                    >
                        {{ $pluginName }}
                    </h3>
                    <p
                        class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-400"
                    >
                        {{ $record['kind'] ?? __('capell-extension-marketplace::marketplace.card.unknown_kind') }}
                    </p>
                </div>

                <div class="flex flex-wrap justify-end gap-1.5">
                    @if ($record['is_installed'] ?? false)
                        <span
                            class="bg-success-50 text-success-700 ring-success-600/20 dark:bg-success-500/10 dark:text-success-400 rounded-md px-2 py-1 text-xs font-medium ring-1"
                        >
                            {{ __('capell-extension-marketplace::marketplace.card.installed') }}
                        </span>
                    @endif

                    @if ($record['has_update_available'] ?? false)
                        <span
                            class="bg-info-50 text-info-700 ring-info-600/20 dark:bg-info-500/10 dark:text-info-300 rounded-md px-2 py-1 text-xs font-medium ring-1"
                            title="{{ __('capell-extension-marketplace::marketplace.card.update_available_tooltip') }}"
                        >
                            {{ __('capell-extension-marketplace::marketplace.card.update_available') }}
                        </span>
                    @endif

                    @if ($record['is_featured'] ?? false)
                        <span
                            class="bg-warning-50 text-warning-700 ring-warning-600/20 dark:bg-warning-500/10 dark:text-warning-400 rounded-md px-2 py-1 text-xs font-medium ring-1"
                        >
                            {{ __('capell-extension-marketplace::marketplace.card.featured') }}
                        </span>
                    @endif

                    @if ($record['is_publisher_verified'] ?? false)
                        <span
                            class="bg-primary-50 text-primary-700 ring-primary-600/20 dark:bg-primary-500/10 dark:text-primary-300 rounded-md px-2 py-1 text-xs font-medium ring-1"
                            title="{{ __('capell-extension-marketplace::marketplace.card.verified_publisher_tooltip') }}"
                        >
                            {{ __('capell-extension-marketplace::marketplace.card.verified_publisher') }}
                        </span>
                    @endif

                    @if ($record['is_security_reviewed'] ?? false)
                        <span
                            class="rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-600/20 dark:bg-white/10 dark:text-gray-200"
                            title="{{ __('capell-extension-marketplace::marketplace.card.security_reviewed_tooltip') }}"
                        >
                            {{ __('capell-extension-marketplace::marketplace.card.security_reviewed') }}
                        </span>
                    @endif
                </div>
            </div>

            <p
                class="mt-3 line-clamp-3 text-sm leading-6 text-gray-600 dark:text-gray-300"
            >
                {{ $record['description'] ?? __('capell-extension-marketplace::marketplace.card.no_description') }}
            </p>
        </div>
    </div>

    <dl class="grid gap-3 text-sm sm:grid-cols-2">
        <div
            class="rounded-lg bg-gray-50 p-3 dark:bg-white/5"
            title="{{ __('capell-extension-marketplace::marketplace.card.version_tooltip') }}"
        >
            <dt
                class="text-xs font-medium uppercase tracking-wide text-gray-400"
            >
                {{ __('capell-extension-marketplace::marketplace.card.version_label') }}
            </dt>
            <dd class="mt-1 font-semibold text-gray-900 dark:text-white">
                @if ($record['is_installed'] ?? false)
                    {{ __('capell-extension-marketplace::marketplace.card.installed_version', ['version' => $record['installed_version'] ?? __('capell-extension-marketplace::marketplace.card.unknown_version')]) }}
                @elseif (is_string($record['latest_version'] ?? null) && $record['latest_version'] !== '')
                    {{ __('capell-extension-marketplace::marketplace.card.latest_version', ['version' => $record['latest_version']]) }}
                @else
                    {{ __('capell-extension-marketplace::marketplace.card.unknown_version') }}
                @endif
            </dd>
        </div>

        <div
            class="rounded-lg bg-gray-50 p-3 dark:bg-white/5"
            title="{{ __('capell-extension-marketplace::marketplace.card.release_tooltip') }}"
        >
            <dt
                class="text-xs font-medium uppercase tracking-wide text-gray-400"
            >
                {{ __('capell-extension-marketplace::marketplace.card.release_label') }}
            </dt>
            <dd class="mt-1 font-semibold text-gray-900 dark:text-white">
                {{ $record['released_at_label'] ?? __('capell-extension-marketplace::marketplace.card.unknown_release') }}
            </dd>
        </div>
    </dl>

    <div class="mt-auto space-y-3">
        @if ($constraints !== [])
            <div
                class="flex flex-wrap gap-1.5"
                title="{{ __('capell-extension-marketplace::marketplace.card.compatibility_tooltip') }}"
            >
                @foreach ($constraints as $constraint)
                    <span
                        class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-white/10 dark:text-gray-300"
                    >
                        {{ $constraint }}
                    </span>
                @endforeach
            </div>
        @endif

        @if ($compatibilityWarnings !== [])
            <div
                class="border-danger-200 bg-danger-50 text-danger-800 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-200 rounded-lg border p-3 text-xs"
                role="alert"
            >
                <p class="font-semibold">
                    {{ __('capell-extension-marketplace::marketplace.card.compatibility_warning_title') }}
                </p>
                <ul class="mt-1 list-disc space-y-1 ps-4">
                    @foreach ($compatibilityWarnings as $compatibilityWarning)
                        <li>{{ $compatibilityWarning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($categoryLabels !== [] || $capabilityLabels !== [])
            <div class="space-y-2">
                @if ($categoryLabels !== [])
                    <div
                        class="flex flex-wrap gap-1.5"
                        title="{{ __('capell-extension-marketplace::marketplace.card.categories_tooltip') }}"
                    >
                        @foreach ($categoryLabels as $categoryLabel)
                            <span
                                class="bg-primary-50 text-primary-700 ring-primary-600/20 dark:bg-primary-500/10 dark:text-primary-300 rounded-full px-2 py-1 text-xs font-medium ring-1"
                            >
                                {{ $categoryLabel }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if ($capabilityLabels !== [])
                    <div
                        class="flex flex-wrap gap-1.5"
                        title="{{ __('capell-extension-marketplace::marketplace.card.capabilities_tooltip') }}"
                    >
                        @foreach ($capabilityLabels as $capabilityLabel)
                            <span
                                class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-white/10 dark:text-gray-300"
                            >
                                {{ $capabilityLabel }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div
            class="flex items-center justify-between gap-3 border-t border-gray-100 pt-3 dark:border-white/10"
        >
            <div>
                <p
                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                >
                    {{ __('capell-extension-marketplace::marketplace.card.price_label') }}
                </p>
                <p
                    class="text-base font-semibold text-gray-950 dark:text-white"
                >
                    {{ $record['price_label'] ?? __('capell-extension-marketplace::marketplace.install.free') }}
                </p>
            </div>

            @if (is_string($record['documentation_url'] ?? null) && $record['documentation_url'] !== '')
                <a
                    href="{{ $record['documentation_url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-primary-600 text-sm font-medium underline underline-offset-4"
                    title="{{ __('capell-extension-marketplace::marketplace.install.documentation_tooltip') }}"
                >
                    {{ __('capell-extension-marketplace::marketplace.install.documentation_link') }}
                </a>
            @endif
        </div>
    </div>
</article>
