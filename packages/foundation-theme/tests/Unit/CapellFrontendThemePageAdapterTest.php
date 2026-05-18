<?php

declare(strict_types=1);

use Capell\Core\ThemeStudio\Data\FooterData;
use Capell\Core\ThemeStudio\Data\HeroSectionData;
use Capell\Core\ThemeStudio\Data\NavigationData;
use Capell\Frontend\ThemeStudio\Adapters\CapellFrontendThemePageAdapter;

it('builds portable fallback theme data for the current frontend page', function (): void {
    $page = (new CapellFrontendThemePageAdapter)->currentPage();
    $heroSection = $page->sections[0];

    expect($page->title)->toBe('Untitled page')
        ->and($page->sections)->toHaveCount(1)
        ->and($heroSection)->toBeInstanceOf(HeroSectionData::class)
        ->and($page->navigation)->toBeInstanceOf(NavigationData::class)
        ->and($page->navigation->brandName)->toBe('Capell')
        ->and($page->footer)->toBeInstanceOf(FooterData::class)
        ->and($page->footer->brandName)->toBe('Capell');

    expect($heroSection->toViewData()['section'])->toBe($heroSection);
});

it('keeps default navigation usable when no package navigation is available', function (): void {
    $navigation = (new CapellFrontendThemePageAdapter)->currentPage()->navigation;

    expect($navigation)->toBeInstanceOf(NavigationData::class)
        ->and($navigation->items)->toBe([
            ['label' => 'Content', 'url' => '#content'],
            ['label' => 'Gallery', 'url' => '#gallery'],
            ['label' => 'Contact', 'url' => '#footer'],
        ]);
});
