<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

it('keeps layout builder livewire internals behind declared adapters', function (): void {
    $rootPath = dirname(__DIR__, 3);
    $packagesPath = $rootPath . '/packages';
    $allowedFiles = [
        'packages/foundation-theme/src/Livewire/Assets/Table/AbstractAssets.php',
    ];
    $allowedLookup = array_fill_keys($allowedFiles, true);
    $violations = [];

    $files = (new Finder)
        ->files()
        ->in($packagesPath)
        ->exclude('layout-builder')
        ->path('/src/')
        ->name('*.php');

    foreach ($files as $file) {
        $relativePath = str_replace($rootPath . '/', '', $file->getPathname());

        if (isset($allowedLookup[$relativePath])) {
            continue;
        }

        if (str_contains($file->getContents(), 'Capell\\LayoutBuilder\\Livewire\\Filament\\')) {
            $violations[] = $relativePath;
        }
    }

    expect($violations)->toBe(
        [],
        'Package source must not depend on LayoutBuilder Livewire internals:' . PHP_EOL .
        json_encode($violations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    );
});
