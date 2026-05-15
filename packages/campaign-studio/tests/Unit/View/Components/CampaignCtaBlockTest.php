<?php

declare(strict_types=1);

use Capell\CampaignStudio\Models\CampaignCtaBlock;
use Capell\CampaignStudio\View\Components\Element\CampaignCtaBlock as CampaignCtaBlockComponent;
use Capell\LayoutBuilder\Models\Element;

it('hydrates CTA blocks for campaign elements in one batch', function (): void {
    $firstCtaBlock = CampaignCtaBlock::factory()->create(['headline' => 'First CTA']);
    $secondCtaBlock = CampaignCtaBlock::factory()->create(['headline' => 'Second CTA']);
    $firstElement = Element::factory()->create(['meta' => ['cta_block_id' => $firstCtaBlock->getKey()]]);
    $secondElement = Element::factory()->create(['meta' => ['cta_block_id' => $secondCtaBlock->getKey()]]);

    CampaignCtaBlockComponent::hydrateElements(collect([$firstElement, $secondElement]));

    expect($firstElement->relationLoaded('campaignCtaBlock'))->toBeTrue()
        ->and($firstElement->getRelation('campaignCtaBlock')->headline)->toBe('First CTA')
        ->and($secondElement->relationLoaded('campaignCtaBlock'))->toBeTrue()
        ->and($secondElement->getRelation('campaignCtaBlock')->headline)->toBe('Second CTA');
});
