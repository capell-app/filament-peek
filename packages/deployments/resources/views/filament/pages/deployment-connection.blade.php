<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Connect buttons --}}
        <div class="flex gap-4">
            <a
                href="{{ $this->getGitHubOAuthUrl() }}"
                class="fi-btn fi-btn-size-md fi-color-primary fi-btn-color-primary"
            >
                {{ __('capell-deployments::plugins.deployment_connection.connect_github') }}
            </a>
            <a
                href="{{ $this->getGitLabOAuthUrl() }}"
                class="fi-btn fi-btn-size-md fi-color-gray fi-btn-color-gray"
            >
                {{ __('capell-deployments::plugins.deployment_connection.connect_gitlab') }}
            </a>
            <a
                href="{{ $this->getBitbucketOAuthUrl() }}"
                class="fi-btn fi-btn-size-md fi-color-gray fi-btn-color-gray"
            >
                {{ __('capell-deployments::plugins.deployment_connection.connect_bitbucket') }}
            </a>
        </div>

        {{-- Active connections --}}
        @foreach ($this->getConnections() as $connection)
            <div
                class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h3
                            class="fi-section-header-heading text-base font-semibold"
                        >
                            {{ $connection->provider->getLabel() }}:
                            {{ $connection->repoCoordinate() }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $connection->install_policy->getLabel() }}
                        </p>
                    </div>
                    <button
                        wire:click="disconnect({{ $connection->id }})"
                        wire:confirm="{{ __('capell-deployments::plugins.deployment_connection.disconnect_confirm') }}"
                        class="fi-btn fi-btn-size-sm fi-color-danger fi-btn-color-danger"
                    >
                        {{ __('capell-deployments::plugins.deployment_connection.disconnect') }}
                    </button>
                </div>
            </div>
        @endforeach

        @if (count($this->getConnections()) === 0)
            <p class="text-sm text-gray-500">
                {{ __('capell-deployments::plugins.deployment_connection.none_connected') }}
            </p>
        @endif
    </div>
</x-filament-panels::page>
