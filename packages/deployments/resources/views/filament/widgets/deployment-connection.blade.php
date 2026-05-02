<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $connection = $this->getConnection();
        @endphp

        @if ($connection)
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('capell-deployments::plugins.deployment_connection.connected_to', ['provider' => $connection->provider->getLabel(), 'repo' => $connection->repoCoordinate()]) }}
            </p>
        @else
            <p class="text-sm text-gray-500">
                {{ __('capell-deployments::plugins.deployment_connection.not_connected') }}
            </p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
