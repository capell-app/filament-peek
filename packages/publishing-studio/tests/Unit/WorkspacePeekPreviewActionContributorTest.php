<?php

declare(strict_types=1);

use Capell\PublishingStudio\Filament\Resources\PublishingStudio\Actions\WorkspacePeekPreviewAction;
use Capell\PublishingStudio\WorkspacePeekPreviewActionContributor;

it('contributes the workspace modal preview action', function (): void {
    $actions = (new WorkspacePeekPreviewActionContributor)->actions();

    expect($actions)
        ->toHaveCount(1)
        ->and($actions[0])->toBeInstanceOf(WorkspacePeekPreviewAction::class);
});
