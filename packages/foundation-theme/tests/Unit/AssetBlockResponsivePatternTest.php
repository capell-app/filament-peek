<?php

declare(strict_types=1);

test('asset block view contains the responsive grid to carousel pattern hooks', function (): void {
    $themePath = dirname(__DIR__, 2);

    $assetBlockView = file_get_contents($themePath . '/resources/views/components/block/asset/index.blade.php');

    expect($assetBlockView)->toContain('ResponsiveAssetLayoutOptions::fromBlock')
        ->and($assetBlockView)->toContain('usesMobileCarousel()')
        ->and($assetBlockView)->toContain('data-carousel-breakpoints')
        ->and($assetBlockView)->toContain('data-carousel-rows');
});
