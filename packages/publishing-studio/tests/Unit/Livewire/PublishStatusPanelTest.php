<?php

declare(strict_types=1);

use Capell\Admin\Data\PagePublishStateData;
use Capell\PublishingStudio\Livewire\PublishStatusPanel;

it('returns the admin publish state DTO for publish panel extenders', function (): void {
    $panel = new PublishStatusPanel;
    $panel->pageId = 999999;

    expect($panel->state())->toBeInstanceOf(PagePublishStateData::class)
        ->and($panel->state()->hasActiveContext())->toBeFalse();
});
