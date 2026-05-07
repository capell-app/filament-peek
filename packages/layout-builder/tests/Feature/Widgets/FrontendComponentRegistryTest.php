<?php

declare(strict_types=1);

use Capell\Frontend\Contracts\FrontendComponentRegistryInterface;
use Capell\LayoutBuilder\Data\WidgetDefinitionData;
use Capell\LayoutBuilder\Enums\FrontendComponentKeyEnum;

it('registers stable section component keys and legacy aliases', function (): void {
    $registry = app(FrontendComponentRegistryInterface::class);

    expect($registry->resolve(FrontendComponentKeyEnum::SectionBlock->value))->toBe('capell-layout-builder::section.block')
        ->and($registry->resolve('capell-content-sections::section.block'))->toBe('capell-layout-builder::section.block')
        ->and($registry->resolve('capell-layout-builder::section.block'))->toBe('capell-layout-builder::section.block')
        ->and($registry->resolve(FrontendComponentKeyEnum::SectionTeamMember->value))->toBe('capell-layout-builder::section.team-member')
        ->and($registry->resolve('capell-content-sections::section.team-member'))->toBe('capell-layout-builder::section.team-member')
        ->and($registry->resolve('capell-layout-builder::section.team-member'))->toBe('capell-layout-builder::section.team-member');
});

it('keeps default widget component items registered', function (): void {
    $registry = app(FrontendComponentRegistryInterface::class);

    $componentItems = collect(WidgetDefinitionData::defaultCatalog())
        ->pluck('meta.component_item')
        ->filter()
        ->unique()
        ->values();

    expect($componentItems)->toContain(FrontendComponentKeyEnum::SectionBlock->value);

    foreach ($componentItems as $componentItem) {
        expect($registry->hasReference($componentItem))->toBeTrue()
            ->and($registry->resolve($componentItem))->toStartWith('capell-layout-builder::section.');
    }
});
