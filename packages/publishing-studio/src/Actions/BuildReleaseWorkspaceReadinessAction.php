<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Checks\PublishCheckPipeline;
use Capell\PublishingStudio\Checks\PublishCheckResult;
use Capell\PublishingStudio\Data\ReleaseWorkspaceReadinessData;
use Capell\PublishingStudio\Enums\WorkspaceStatusEnum;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\Publisher;
use Capell\PublishingStudio\Rebaser;
use Capell\PublishingStudio\ReleaseWindowGuard;
use Capell\PublishingStudio\WorkspaceRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsObject;

final class BuildReleaseWorkspaceReadinessAction
{
    use AsObject;

    public function handle(
        Workspace $workspace,
        ?int $workspaceRowCount = null,
        bool $canBypassReleaseWindow = false,
    ): ReleaseWorkspaceReadinessData {
        $blockingIssues = [];
        $workspaceRowCount ??= $this->countWorkspaceRows($workspace);

        if ($workspaceRowCount === 0) {
            $blockingIssues[] = __('capell-admin::workspace.release.blocking.empty_release');
        }

        if ($workspace->status !== WorkspaceStatusEnum::Approved && $workspace->status !== WorkspaceStatusEnum::Scheduled) {
            $blockingIssues[] = __('capell-admin::workspace.release.blocking.not_approved', [
                'status' => $workspace->status->getLabel(),
            ]);
        }

        if ($workspace->embargo_until !== null && $workspace->embargo_until->isFuture()) {
            $blockingIssues[] = __('capell-admin::workspace.release.blocking.embargoed', [
                'datetime' => $workspace->embargo_until->toDateTimeString(),
            ]);
        }

        $releaseWindowGuard = new ReleaseWindowGuard;

        if (! $releaseWindowGuard->isOpen() && ! $canBypassReleaseWindow) {
            $blockingIssues[] = __('capell-admin::workspace.release.blocking.release_window_closed');
        }

        $collisions = app(Publisher::class)->detectUrlCollisions($workspace);

        foreach ($collisions as $collision) {
            $blockingIssues[] = __('capell-admin::workspace.release.blocking.url_collision', [
                'url' => $collision['url'],
            ]);
        }

        $rebaseReport = app(Rebaser::class)->analyse($workspace);

        if ($rebaseReport->hasConflicts()) {
            $blockingIssues[] = __('capell-admin::workspace.release.blocking.stale_conflicts', [
                'count' => $rebaseReport->conflictCount(),
            ]);
        }

        foreach (app(PublishCheckPipeline::class)->run($workspace) as $checkResult) {
            if (! $checkResult instanceof PublishCheckResult || ! $checkResult->isError() || $checkResult->isClean()) {
                continue;
            }

            array_push(
                $blockingIssues,
                ...($checkResult->messages === [] ? [$checkResult->label] : $checkResult->messages),
            );
        }

        $blockingIssues = array_values(array_filter(
            $blockingIssues,
            static fn (string $blockingIssue): bool => $blockingIssue !== '',
        ));

        return new ReleaseWorkspaceReadinessData(
            workspaceId: (int) $workspace->getKey(),
            wouldPublish: $blockingIssues === [],
            blockingIssues: $blockingIssues,
            blockingIssueCount: count($blockingIssues),
        );
    }

    private function countWorkspaceRows(Workspace $workspace): int
    {
        $rowCount = 0;

        foreach (array_keys(WorkspaceRegistry::all()) as $modelClass) {
            /** @var class-string<Model> $modelClass */
            $model = new $modelClass;

            if (! DB::getSchemaBuilder()->hasTable($model->getTable())) {
                continue;
            }

            $rowCount += $modelClass::query()
                ->withoutGlobalScopes()
                ->where('workspace_id', $workspace->id)
                ->when($this->usesSoftDeletes($model), static fn (Builder $query): Builder => $query->withTrashed())
                ->count();
        }

        return $rowCount;
    }

    private function usesSoftDeletes(Model $model): bool
    {
        $traitNames = array_map(
            static fn (string $traitName): string => ltrim($traitName, '\\'),
            class_uses_recursive($model),
        );

        return in_array(SoftDeletes::class, $traitNames, true);
    }
}
