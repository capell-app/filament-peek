<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Livewire;

use Capell\PublishingStudio\Actions\BuildReleaseWorkspaceReadinessAction;
use Capell\PublishingStudio\Actions\BuildReleaseWorkspaceSummaryAction;
use Capell\PublishingStudio\Enums\WorkspaceKindEnum;
use Capell\PublishingStudio\Models\Workspace;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ReleaseWorkspaceSummaryPanel extends Component
{
    public ?int $workspaceId = null;

    public ?Workspace $workspace = null;

    public function mount(?Workspace $record = null, ?Workspace $workspace = null): void
    {
        $this->workspace = $record ?? $workspace ?? $this->workspace;
        $this->workspaceId = $this->workspace?->getKey();
    }

    public function render(): View
    {
        $workspace = $this->workspace();

        if (! $workspace instanceof Workspace || $workspace->kind !== WorkspaceKindEnum::Release) {
            return view('capell-publishing-studio::livewire.release-workspace-summary-panel', [
                'visible' => false,
            ]);
        }

        return view('capell-publishing-studio::livewire.release-workspace-summary-panel', [
            'visible' => true,
            'summary' => BuildReleaseWorkspaceSummaryAction::run($workspace),
            'readiness' => BuildReleaseWorkspaceReadinessAction::run($workspace),
        ]);
    }

    private function workspace(): ?Workspace
    {
        if ($this->workspaceId === null) {
            return null;
        }

        return $this->workspace ?? Workspace::query()->find($this->workspaceId);
    }
}
