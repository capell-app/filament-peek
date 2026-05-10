<?php

declare(strict_types=1);

use Capell\PublishingStudio\Actions\BuildReleaseWorkspaceSummaryAction;
use Capell\PublishingStudio\Enums\WorkspaceKindEnum;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\Tests\Integration\Fixtures\ShadowableDraftableFixture;
use Capell\PublishingStudio\Tests\Integration\Fixtures\WorkspaceDraftableFixture;
use Capell\PublishingStudio\WorkspaceRegistry;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

beforeEach(function (): void {
    Schema::create('workspace_draftable_fixtures', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('workspace_id')->default(0)->index();
        $table->unsignedBigInteger('shadowed_by_workspace_id')->default(0)->index();
        $table->uuid('uuid');
        $table->string('name');
        $table->timestamps();
    });

    Schema::create('shadowable_draftable_fixtures', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('workspace_id')->default(0)->index();
        $table->unsignedBigInteger('shadowed_by_workspace_id')->default(0)->index();
        $table->uuid('uuid');
        $table->string('name');
        $table->timestamps();
        $table->softDeletes();
    });

    WorkspaceRegistry::reset();
    WorkspaceRegistry::register(WorkspaceDraftableFixture::class);
    WorkspaceRegistry::register(ShadowableDraftableFixture::class);
});

afterEach(function (): void {
    Schema::dropIfExists('shadowable_draftable_fixtures');
    Schema::dropIfExists('workspace_draftable_fixtures');
    WorkspaceRegistry::reset();
});

it('summarises registered draftable rows inside a release workspace', function (): void {
    $workspace = Workspace::factory()->create(['kind' => WorkspaceKindEnum::Release]);

    WorkspaceDraftableFixture::query()
        ->withoutGlobalScopes()
        ->create([
            'workspace_id' => $workspace->id,
            'shadowed_by_workspace_id' => $workspace->id,
            'uuid' => (string) Str::uuid(),
            'name' => 'Campaign landing page',
        ]);

    $summary = BuildReleaseWorkspaceSummaryAction::run($workspace);

    expect($summary->workspaceId)->toBe($workspace->id)
        ->and($summary->items)->toHaveCount(1)
        ->and($summary->items[0]->label)->toContain('Campaign landing page')
        ->and($summary->items[0]->changeType)->toBe('updated');
});

it('classifies workspace rows using the production shadow column default', function (): void {
    $workspace = Workspace::factory()->create(['kind' => WorkspaceKindEnum::Release]);

    WorkspaceDraftableFixture::query()
        ->withoutGlobalScopes()
        ->create([
            'workspace_id' => $workspace->id,
            'shadowed_by_workspace_id' => 0,
            'uuid' => (string) Str::uuid(),
            'name' => 'New release page',
        ]);

    $summary = BuildReleaseWorkspaceSummaryAction::run($workspace);

    expect($summary->items)->toHaveCount(1)
        ->and($summary->items[0]->changeType)->toBe('created');
});

it('classifies soft deleted workspace tombstones as deleted', function (): void {
    $workspace = Workspace::factory()->create(['kind' => WorkspaceKindEnum::Release]);

    $record = ShadowableDraftableFixture::query()
        ->withoutGlobalScopes()
        ->create([
            'workspace_id' => $workspace->id,
            'shadowed_by_workspace_id' => 0,
            'uuid' => (string) Str::uuid(),
            'name' => 'Retired release page',
        ]);

    $record->delete();

    $summary = BuildReleaseWorkspaceSummaryAction::run($workspace);

    expect($summary->items)->toHaveCount(1)
        ->and($summary->items[0]->changeType)->toBe('deleted');
});

it('caps rendered release items while keeping the total count', function (): void {
    $workspace = Workspace::factory()->create(['kind' => WorkspaceKindEnum::Release]);

    foreach (range(1, 30) as $itemNumber) {
        WorkspaceDraftableFixture::query()
            ->withoutGlobalScopes()
            ->create([
                'workspace_id' => $workspace->id,
                'shadowed_by_workspace_id' => 0,
                'uuid' => (string) Str::uuid(),
                'name' => sprintf('Release item %d', $itemNumber),
            ]);
    }

    $summary = BuildReleaseWorkspaceSummaryAction::run($workspace, 25);

    expect($summary->items)->toHaveCount(25)
        ->and($summary->itemCount)->toBe(30);
});

it('returns an empty release summary for a workspace without staged rows', function (): void {
    $workspace = Workspace::factory()->create(['kind' => WorkspaceKindEnum::Release]);

    $summary = BuildReleaseWorkspaceSummaryAction::run($workspace);

    expect($summary->workspaceId)->toBe($workspace->id)
        ->and($summary->items)->toBe([])
        ->and($summary->itemCount)->toBe(0);
});
