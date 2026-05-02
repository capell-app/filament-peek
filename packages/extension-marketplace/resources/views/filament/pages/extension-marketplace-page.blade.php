<x-filament-panels::page>
    @php
        $marketplaceConnectionState = $this->marketplaceConnectionState();
        $pendingMarketplaceRegistration = $this->pendingMarketplaceRegistration();
        $marketplaceInstance = $this->marketplaceInstance();
        $compatibilityVersions = $this->detectedCompatibilityVersions();
    @endphp

    <section
        @class([
            'rounded-xl border p-4 shadow-sm',
            'border-danger-200 bg-danger-50 text-danger-950 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-100' => $marketplaceConnectionState === 'needs_configuration',
            'border-warning-200 bg-warning-50 text-warning-950 dark:border-warning-500/40 dark:bg-warning-500/10 dark:text-warning-100' => in_array($marketplaceConnectionState, ['not_connected', 'pending'], true),
            'border-success-200 bg-success-50 text-success-950 dark:border-success-500/40 dark:bg-success-500/10 dark:text-success-100' => $marketplaceConnectionState === 'connected',
        ])
        role="alert"
    >
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"
        >
            <div class="max-w-4xl space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="text-xs font-semibold uppercase tracking-wide opacity-75"
                    >
                        {{ __('capell-extension-marketplace::marketplace.marketplace.status_badge') }}
                    </span>
                    <span
                        @class([
                            'rounded-full px-2.5 py-1 text-xs font-semibold ring-1',
                            'bg-danger-100 text-danger-800 ring-danger-300 dark:bg-danger-400/10 dark:text-danger-200 dark:ring-danger-400/40' => $marketplaceConnectionState === 'needs_configuration',
                            'bg-warning-100 text-warning-800 ring-warning-300 dark:bg-warning-400/10 dark:text-warning-200 dark:ring-warning-400/40' => in_array($marketplaceConnectionState, ['not_connected', 'pending'], true),
                            'bg-success-100 text-success-800 ring-success-300 dark:bg-success-400/10 dark:text-success-200 dark:ring-success-400/40' => $marketplaceConnectionState === 'connected',
                        ])
                    >
                        {{ __('capell-extension-marketplace::marketplace.marketplace.status.' . $marketplaceConnectionState . '.label') }}
                    </span>
                </div>

                <div>
                    <h2 class="text-base font-semibold">
                        {{ $this->marketplaceConnectionTitle() }}
                    </h2>
                    <p class="mt-1 text-sm opacity-80">
                        {{ $this->marketplaceConnectionBody() }}
                    </p>
                </div>

                @if ($pendingMarketplaceRegistration !== null || $marketplaceInstance !== null)
                    <div class="flex flex-wrap gap-2 text-xs opacity-80">
                        @if ($pendingMarketplaceRegistration?->expires_at)
                            <span>
                                {{ __('capell-extension-marketplace::marketplace.marketplace.expires_on', ['date' => $pendingMarketplaceRegistration->expires_at->toFormattedDateString()]) }}
                            </span>
                        @endif

                        @if ($marketplaceInstance !== null)
                            <span>
                                {{ __('capell-extension-marketplace::marketplace.marketplace.instance_id', ['id' => $marketplaceInstance->instance_id]) }}
                            </span>
                            <span>
                                @if ($marketplaceInstance->last_heartbeat_at)
                                    {{ __('capell-extension-marketplace::marketplace.marketplace.last_heartbeat', ['date' => $marketplaceInstance->last_heartbeat_at->toFormattedDateString()]) }}
                                @else
                                    {{ __('capell-extension-marketplace::marketplace.marketplace.no_heartbeat') }}
                                @endif
                            </span>
                        @endif
                    </div>
                @endif

                <div class="grid gap-2 text-sm opacity-90 md:grid-cols-3">
                    <div>
                        <span class="font-semibold">1.</span>
                        {{ __('capell-extension-marketplace::marketplace.marketplace.steps.connect') }}
                    </div>
                    <div>
                        <span class="font-semibold">2.</span>
                        {{ __('capell-extension-marketplace::marketplace.marketplace.steps.authorize') }}
                    </div>
                    <div>
                        <span class="font-semibold">3.</span>
                        {{ __('capell-extension-marketplace::marketplace.marketplace.steps.install') }}
                    </div>
                </div>
            </div>

            <div class="flex shrink-0 flex-wrap gap-2">
                @if (in_array($marketplaceConnectionState, ['needs_configuration', 'not_connected'], true))
                    {{ $this->connectMarketplaceAction }}
                @endif

                @if ($marketplaceConnectionState === 'pending')
                    @if (is_string($pendingMarketplaceRegistration?->verification_url) && $pendingMarketplaceRegistration->verification_url !== '')
                        <a
                            href="{{ $pendingMarketplaceRegistration->verification_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            title="{{ __('capell-extension-marketplace::marketplace.marketplace.open_marketplace_tooltip') }}"
                            class="fi-btn fi-btn-size-md fi-color-gray fi-btn-color-gray"
                        >
                            {{ __('capell-extension-marketplace::marketplace.marketplace.open_marketplace') }}
                        </a>
                    @endif

                    {{ $this->verifyMarketplaceDomainAction }}
                @endif

                @if ($marketplaceConnectionState === 'connected')
                    {{ $this->runMarketplaceHeartbeatAction }}
                @endif
            </div>
        </div>
    </section>

    <section
        class="border-info-200 bg-info-50 text-info-950 dark:border-info-500/40 dark:bg-info-500/10 dark:text-info-100 rounded-xl border p-4 shadow-sm"
    >
        <div
            class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between"
        >
            <div>
                <h2 class="text-sm font-semibold">
                    {{ __('capell-extension-marketplace::marketplace.explorer.title') }}
                </h2>
                <p class="mt-1 text-sm opacity-80">
                    {{ __('capell-extension-marketplace::marketplace.explorer.description') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs">
                @foreach ($compatibilityVersions as $platform => $version)
                    @if (is_string($version) && $version !== '')
                        <span
                            class="ring-info-200 dark:ring-info-500/30 rounded-full bg-white/70 px-2.5 py-1 font-medium ring-1 dark:bg-white/10"
                        >
                            {{ __('capell-extension-marketplace::marketplace.explorer.compatibility_' . $platform, ['version' => $version]) }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    {{ $this->table }}

    <x-filament-actions::modals />
</x-filament-panels::page>
