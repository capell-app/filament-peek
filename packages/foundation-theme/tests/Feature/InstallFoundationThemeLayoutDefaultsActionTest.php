<?php

declare(strict_types=1);

use Capell\Core\Enums\LayoutEnum;
use Capell\Core\Models\Layout;
use Capell\FoundationTheme\Actions\InstallFoundationThemeLayoutDefaultsAction;
use Capell\LayoutBuilder\Models\Block;
use Capell\LayoutBuilder\Models\BlockAsset;
use Illuminate\Database\Eloquent\Relations\Relation;

it('registers layout builder morph models during a same-process fresh install', function (): void {
    $originalMorphMap = Relation::morphMap();
    $morphMapWithoutLayoutBuilder = array_filter(
        $originalMorphMap,
        static fn (string $model): bool => ! in_array($model, [Block::class, BlockAsset::class], true),
    );

    Relation::morphMap($morphMapWithoutLayoutBuilder, merge: false);

    try {
        $result = InstallFoundationThemeLayoutDefaultsAction::run();

        expect($result['created'])->toBeGreaterThanOrEqual(2)
            ->and(Relation::getMorphedModel('block'))->toBe(Block::class)
            ->and(Relation::getMorphedModel('block_asset'))->toBe(BlockAsset::class)
            ->and(Layout::query()->where('key', LayoutEnum::Home->value)->firstOrFail()->blocks)->toBe(['page-content']);
    } finally {
        Relation::morphMap($originalMorphMap, merge: false);
    }
});
