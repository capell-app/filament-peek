<?php

declare(strict_types=1);

use Capell\PublishingStudio\Actions\BuildReleaseWorkspaceReadinessAction;
use Capell\PublishingStudio\Enums\WorkspaceKindEnum;
use Capell\PublishingStudio\Enums\WorkspaceStatusEnum;
use Capell\PublishingStudio\Events\WorkspaceStateChanged;
use Capell\PublishingStudio\Models\Version;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\Tests\Integration\Fixtures\ShadowableDraftableFixture;
use Capell\PublishingStudio\Tests\Integration\Fixtures\WorkspaceDraftableFixture;
use Capell\PublishingStudio\WorkspaceRegistry;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

beforeEach(function (): void {
    Schema::create('workspace_draftable_fixtures', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('workspace_id')->default(0)->index();
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

it('marks an approved release workspace ready when dry run would publish', function (): void {
    Event::fake([WorkspaceStateChanged::class]);

    $workspace = Workspace::factory()->create([
        'kind' => WorkspaceKindEnum::Release,
        'status' => WorkspaceStatusEnum::Approved,
        'base_version_id' => Version::liveId(),
    ]);

    WorkspaceDraftableFixture::query()
        ->withoutGlobalScopes()
        ->create([
            'workspace_id' => $workspace->id,
            'uuid' => (string) Str::uuid(),
            'name' => 'Launch draft',
        ]);

    $readiness = BuildReleaseWorkspaceReadinessAction::run($workspace);

    expect($readiness->workspaceId)->toBe($workspace->id)
        ->and($readiness->wouldPublish)->toBeTrue()
        ->and($readiness->blockingIssueCount)->toBe(0);

    expect($workspace->refresh()->status)->toBe(WorkspaceStatusEnum::Approved);

    Event::assertNotDispatched(WorkspaceStateChanged::class);
});

it('reports embargoed release workspaces as blocked', function (): void {
    $workspace = Workspace::factory()->create([
        'kind' => WorkspaceKindEnum::Release,
        'status' => WorkspaceStatusEnum::Approved,
        'embargo_until' => now()->addDay(),
    ]);

    WorkspaceDraftableFixture::query()
        ->withoutGlobalScopes()
        ->create([
            'workspace_id' => $workspace->id,
            'uuid' => (string) Str::uuid(),
            'name' => 'Embargoed launch draft',
        ]);

    $readiness = BuildReleaseWorkspaceReadinessAction::run($workspace);

    expect($readiness->wouldPublish)->toBeFalse()
        ->and($readiness->blockingIssueCount)->toBeGreaterThan(0)
        ->and($readiness->blockingIssues[0])->toContain('embargo');
});

it('blocks release workspaces without staged items', function (): void {
    $workspace = Workspace::factory()->create([
        'kind' => WorkspaceKindEnum::Release,
        'status' => WorkspaceStatusEnum::Approved,
        'base_version_id' => Version::liveId(),
    ]);

    $readiness = BuildReleaseWorkspaceReadinessAction::run($workspace);

    expect($readiness->wouldPublish)->toBeFalse()
        ->and($readiness->blockingIssues)->toContain(__('capell-admin::workspace.release.blocking.empty_release'));
});

it('treats soft deleted workspace tombstones as staged release items', function (): void {
    $workspace = Workspace::factory()->create([
        'kind' => WorkspaceKindEnum::Release,
        'status' => WorkspaceStatusEnum::Approved,
        'base_version_id' => Version::liveId(),
    ]);

    $record = ShadowableDraftableFixture::query()
        ->withoutGlobalScopes()
        ->create([
            'workspace_id' => $workspace->id,
            'shadowed_by_workspace_id' => 0,
            'uuid' => (string) Str::uuid(),
            'name' => 'Deleted release item',
        ]);

    $record->delete();

    $readiness = BuildReleaseWorkspaceReadinessAction::run($workspace);

    expect($readiness->blockingIssues)->not->toContain(__('capell-admin::workspace.release.blocking.empty_release'));
});

it('does not block a closed release window when the caller can bypass it', function (): void {
    config()->set('capell.publishing-studio.release_windows.enabled', true);
    config()->set('capell.publishing-studio.release_windows.timezone', 'UTC');
    config()->set('capell.publishing-studio.release_windows.windows', [
        ['days' => ['mon'], 'start' => '00:00', 'end' => '00:01'],
    ]);

    $workspace = Workspace::factory()->create([
        'kind' => WorkspaceKindEnum::Release,
        'status' => WorkspaceStatusEnum::Approved,
        'base_version_id' => Version::liveId(),
    ]);

    WorkspaceDraftableFixture::query()
        ->withoutGlobalScopes()
        ->create([
            'workspace_id' => $workspace->id,
            'uuid' => (string) Str::uuid(),
            'name' => 'Window bypass item',
        ]);

    $readiness = BuildReleaseWorkspaceReadinessAction::run($workspace, workspaceRowCount: 1, canBypassReleaseWindow: true);

    expect($readiness->blockingIssues)->not->toContain(__('capell-admin::workspace.release.blocking.release_window_closed'));
});
