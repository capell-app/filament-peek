<?php

declare(strict_types=1);

test('asset element view contains the responsive grid to carousel pattern hooks', function (): void {
    $themePath = dirname(__DIR__, 2);

    $assetElementView = file_get_contents($themePath . '/resources/views/components/element/asset/index.blade.php');

    expect($assetElementView)->toContain('ResponsiveAssetLayoutOptions::fromElement')
        ->and($assetElementView)->toContain('usesMobileCarousel()')
        ->and($assetElementView)->toContain('data-carousel-breakpoints')
        ->and($assetElementView)->toContain('data-carousel-rows');
});
