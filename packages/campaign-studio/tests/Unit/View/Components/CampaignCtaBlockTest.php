<?php

declare(strict_types=1);

use Capell\CampaignStudio\Models\CampaignCtaBlock;
use Capell\CampaignStudio\View\Components\Widget\CampaignCtaBlock as CampaignCtaBlockComponent;
use Capell\Core\Models\Widget;

it('hydrates CTA blocks for campaign widgets in one batch', function (): void {
    $firstCtaBlock = CampaignCtaBlock::factory()->create(['headline' => 'First CTA']);
    $secondCtaBlock = CampaignCtaBlock::factory()->create(['headline' => 'Second CTA']);
    $firstWidget = Widget::factory()->create(['meta' => ['cta_block_id' => $firstCtaBlock->getKey()]]);
    $secondWidget = Widget::factory()->create(['meta' => ['cta_block_id' => $secondCtaBlock->getKey()]]);

    CampaignCtaBlockComponent::hydrateWidgets(collect([$firstWidget, $secondWidget]));

    expect($firstWidget->relationLoaded('campaignCtaBlock'))->toBeTrue()
        ->and($firstWidget->getRelation('campaignCtaBlock')->headline)->toBe('First CTA')
        ->and($secondWidget->relationLoaded('campaignCtaBlock'))->toBeTrue()
        ->and($secondWidget->getRelation('campaignCtaBlock')->headline)->toBe('Second CTA');
});
