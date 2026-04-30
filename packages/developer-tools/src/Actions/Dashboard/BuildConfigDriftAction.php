<?php

declare(strict_types=1);

namespace Capell\DeveloperTools\Actions\Dashboard;

use Capell\DeveloperTools\Data\Dashboard\ConfigDriftData;
use Capell\DeveloperTools\Data\Dashboard\ConfigDriftEntryData;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\DataCollection;

/**
 * @method static ConfigDriftData run()
 */
class BuildConfigDriftAction
{
    use AsAction;

    public function handle(): ConfigDriftData
    {
        $allDrifts = [];
        $packagesChecked = 0;

        foreach ($this->configPairs() as [$packageName, $shippedPath, $hostPath]) {
            if (! File::exists($shippedPath)) {
                continue;
            }

            if (! File::exists($hostPath)) {
                // Host hasn't published this config — skip silently.
                continue;
            }

            /** @var array<string, mixed> $shipped */
            $shipped = require $shippedPath;

            /** @var array<string, mixed> $host */
            $host = require $hostPath;

            $packagesChecked++;

            $drifts = $this->diffArrays($shipped, $host, $packageName);
            $allDrifts = array_merge($allDrifts, $drifts);
        }

        return new ConfigDriftData(
            entries: ConfigDriftEntryData::collect($allDrifts, DataCollection::class),
            totalDriftCount: count($allDrifts),
            packagesChecked: $packagesChecked,
        );
    }

    /**
     * Returns triples of [shortName, shippedConfigAbsPath, hostConfigAbsPath].
     *
     * @return list<array{0: string, 1: string, 2: string}>
     */
    protected function configPairs(): array
    {
        $base = base_path();

        return [
            ['core', $base . '/packages/core/config/capell.php', config_path('capell.php')],
            ['admin', $base . '/packages/admin/config/capell-admin.php', config_path('capell-admin.php')],
            ['frontend', $base . '/packages/frontend/config/capell-frontend.php', config_path('capell-frontend.php')],
        ];
    }

    /**
     * Recursively diff two config arrays. Emits drift entries for keys present
     * in one array but absent in the other. Value differences are intentionally
     * ignored — host apps are expected to override values.
     *
     * @param  array<string, mixed>  $shipped
     * @param  array<string, mixed>  $host
     * @return list<ConfigDriftEntryData>
     */
    private function diffArrays(array $shipped, array $host, string $package, string $prefix = ''): array
    {
        $drifts = [];

        foreach ($shipped as $key => $shippedValue) {
            $path = $prefix === '' ? $key : sprintf('%s.%s', $prefix, $key);

            if (! array_key_exists($key, $host)) {
                $drifts[] = new ConfigDriftEntryData(
                    package: $package,
                    keyPath: $path,
                    kind: 'missing',
                    shippedValue: $this->represent($shippedValue),
                    hostValue: null,
                );

                continue;
            }

            if (is_array($shippedValue) && is_array($host[$key])) {
                /** @var array<string, mixed> $shippedNested */
                $shippedNested = $shippedValue;
                /** @var array<string, mixed> $hostNested */
                $hostNested = $host[$key];
                $drifts = array_merge($drifts, $this->diffArrays($shippedNested, $hostNested, $package, $path));
            }
        }

        foreach ($host as $key => $hostValue) {
            $path = $prefix === '' ? $key : sprintf('%s.%s', $prefix, $key);

            if (! array_key_exists($key, $shipped)) {
                $drifts[] = new ConfigDriftEntryData(
                    package: $package,
                    keyPath: $path,
                    kind: 'stale',
                    shippedValue: null,
                    hostValue: $this->represent($hostValue),
                );
            }
        }

        return $drifts;
    }

    private function represent(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_array($value)) {
            return 'array(' . count($value) . ')';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return gettype($value);
    }
}
