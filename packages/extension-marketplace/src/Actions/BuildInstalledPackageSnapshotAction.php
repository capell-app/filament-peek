<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Actions;

use Capell\Core\Data\PackageData;
use Capell\Core\Facades\CapellCore;
use Capell\ExtensionMarketplace\Data\InstalledPackageData;
use Lorisleiva\Actions\Concerns\AsAction;

final class BuildInstalledPackageSnapshotAction
{
    use AsAction;

    /**
     * @return array<int, InstalledPackageData>
     */
    public function handle(): array
    {
        return CapellCore::getInstalledPackages()
            ->values()
            ->map(
                fn (PackageData $package): InstalledPackageData => new InstalledPackageData(
                    name: $package->name,
                    label: $package->getLabel(),
                    version: $package->version,
                    path: $package->path,
                ),
            )
            ->all();
    }
}
