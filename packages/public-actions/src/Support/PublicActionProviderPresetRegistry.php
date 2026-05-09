<?php

declare(strict_types=1);

namespace Capell\PublicActions\Support;

use Capell\PublicActions\Data\PublicActionProviderPresetData;

final class PublicActionProviderPresetRegistry
{
    /**
     * @return array<string, PublicActionProviderPresetData>
     */
    public function all(): array
    {
        $presets = config('capell-public-actions.adapters.presets', []);

        if (! is_array($presets)) {
            return [];
        }

        return collect($presets)
            ->filter(fn (mixed $preset, mixed $key): bool => is_string($key) && is_array($preset))
            ->mapWithKeys(fn (array $preset, string $key): array => [$key => $this->normalize($key, $preset)])
            ->all();
    }

    public function get(string $key): ?PublicActionProviderPresetData
    {
        return $this->all()[$key] ?? null;
    }

    /**
     * @param  array<string, mixed>  $preset
     */
    private function normalize(string $key, array $preset): PublicActionProviderPresetData
    {
        $adapter = $preset['adapter'] ?? 'http_webhook';
        $method = $preset['method'] ?? 'POST';

        return new PublicActionProviderPresetData(
            key: $key,
            adapter: is_string($adapter) && $adapter !== '' ? $adapter : 'http_webhook',
            method: is_string($method) && $method !== '' ? strtoupper($method) : 'POST',
            expectsJson: (bool) ($preset['expects_json'] ?? true),
        );
    }
}
