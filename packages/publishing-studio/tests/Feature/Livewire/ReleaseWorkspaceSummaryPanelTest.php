<?php

declare(strict_types=1);

use Capell\PublishingStudio\Enums\WorkspaceKindEnum;
use Capell\PublishingStudio\Livewire\ReleaseWorkspaceSummaryPanel;
use Capell\PublishingStudio\Models\Workspace;

use function Pest\Livewire\livewire;

it('renders release summary and readiness for release workspaces', function (): void {
    $workspace = Workspace::factory()->create(['kind' => WorkspaceKindEnum::Release]);

    livewire(ReleaseWorkspaceSummaryPanel::class, ['record' => $workspace])
        ->assertSee(__('capell-admin::workspace.release.summary_title'))
        ->assertSee(__('capell-admin::workspace.release.readiness_title'));
});

it('does not expose release panel content for manual workspaces', function (): void {
    $workspace = Workspace::factory()->create(['kind' => WorkspaceKindEnum::Manual]);

    livewire(ReleaseWorkspaceSummaryPanel::class, ['record' => $workspace])
        ->assertDontSee(__('capell-admin::workspace.release.summary_title'));
});
