<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

it('does not load package migrations directly from package providers', function (): void {
    $providers = (new Finder)
        ->in(__DIR__ . '/../../../packages')
        ->path('/src/')
        ->name('*ServiceProvider.php');

    $offenders = [];

    foreach ($providers as $provider) {
        $contents = $provider->getContents();

        if (str_contains($contents, 'loadMigrationsFrom(')) {
            $offenders[] = $provider->getRelativePathname();
        }
    }

    sort($offenders);

    expect($offenders)->toBe(
        [],
        'Package migrations must be published into the local app database/migrations directory before they are run:' .
        "\n" . json_encode($offenders, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    );
});
