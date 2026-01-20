<?php

declare(strict_types=1);

use Capell\Admin\Support\Context\ContentActionContext;
use Capell\Assistant\Actions\ApplyAiDraftAction;

uses()->group('admin-ai');

it('applies a draft and dispatches event', function (): void {
    // Target with a public content property to satisfy the action contract
    $target = new class
    {
        public string $content = 'Old content';

        public function save(): bool
        {
            return true;
        }
    };

    $context = new ContentActionContext('New improved content');

    $applied = ApplyAiDraftAction::run($context, ['target' => $target]);

    expect($applied)->toBeTrue()
        ->and($target->content)->toBe('New improved content');
});
