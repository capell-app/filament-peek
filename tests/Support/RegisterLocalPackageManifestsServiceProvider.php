<?php

declare(strict_types=1);

namespace Capell\Tests\Support;

use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Manifest\ManifestLoader;
use Capell\Core\Support\Manifest\ManifestValidator;
use Illuminate\Support\ServiceProvider;
use Override;

final class RegisterLocalPackageManifestsServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $loader = new ManifestLoader(new ManifestValidator);
        $manifestPaths = glob(__DIR__ . '/../../packages/*/capell.json');

        foreach ($manifestPaths === false ? [] : $manifestPaths as $manifestPath) {
            $manifest = $loader->load($manifestPath);

            CapellCore::registerManifestPackage(
                $manifest,
                CapellCore::getInstalledPrettyVersion($manifest->name),
            );
        }
    }
}
