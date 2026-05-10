<?php

declare(strict_types=1);

use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\Type;
use Capell\MigrationAssistant\Data\ExportOptions;
use Capell\MigrationAssistant\Services\Export\PageExportService;
use Capell\MigrationAssistant\Services\Import\PackageReader;
use Capell\MigrationAssistant\Services\Import\PackageReadResult;
use Capell\MigrationAssistant\Services\Import\PageImportService;
use Capell\MigrationAssistant\Services\Import\ResolutionMap;
use Capell\MigrationAssistant\Services\Import\Resolvers\MatchResolution;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\Support\PublishingStudioMigrationAssistantContextResolver;
use Capell\PublishingStudio\WorkspaceContext;
use Illuminate\Support\Str;

it('forces live context when no export source workspace is selected', function (): void {
    $workspace = Workspace::factory()->create();
    WorkspaceContext::set($workspace);

    $result = (new PublishingStudioMigrationAssistantContextResolver)->wrap(
        fn (): ?int => WorkspaceContext::currentId(),
    );

    expect($result)->toBeNull()
        ->and(WorkspaceContext::current()?->id)->toBe($workspace->id);
});

it('maps selected live page ids to workspace draft ids for workspace exports', function (): void {
    $workspace = Workspace::factory()->create();
    $uuid = (string) Str::uuid();

    $livePage = Page::factory()->create([
        'uuid' => $uuid,
        'workspace_id' => 0,
        'shadowed_by_workspace_id' => $workspace->id,
    ]);

    $workspacePage = Page::factory()->create([
        'uuid' => $uuid,
        'workspace_id' => $workspace->id,
        'shadowed_by_workspace_id' => 0,
    ]);

    $resolvedIds = (new PublishingStudioMigrationAssistantContextResolver)->resolvePageIds(
        [$livePage->getKey()],
        $workspace->id,
    );

    expect($resolvedIds)->toBe([$workspacePage->getKey()]);
});

it('exports workspace draft pages when a source workspace is selected', function (): void {
    $workspace = Workspace::factory()->create();
    $site = Site::factory()->create();
    $uuid = (string) Str::uuid();

    $livePage = Page::factory()
        ->recycle($site)
        ->create([
            'name' => 'Live page',
            'uuid' => $uuid,
            'workspace_id' => 0,
            'shadowed_by_workspace_id' => $workspace->id,
        ]);

    Page::factory()
        ->recycle($site)
        ->create([
            'name' => 'Workspace page',
            'uuid' => $uuid,
            'workspace_id' => $workspace->id,
            'shadowed_by_workspace_id' => 0,
        ]);

    $path = (new PageExportService(new PublishingStudioMigrationAssistantContextResolver))->exportPages(
        [$livePage->getKey()],
        new ExportOptions(includeMedia: false, sourceWorkspace: $workspace->id),
    );

    $package = (new PackageReader)->read($path);
    $pageEntry = collect($package->payload)
        ->first(fn (string $contents, string $entryPath): bool => str_starts_with($entryPath, 'pages/'));

    $pagePayload = json_decode((string) $pageEntry, true, 512, JSON_THROW_ON_ERROR);

    expect($pagePayload['attributes']['name'] ?? null)->toBe('Workspace page');
});

it('runs page imports inside the target workspace context', function (): void {
    $workspace = Workspace::factory()->create();
    $layout = Layout::factory()->create();
    $type = Type::factory()->create();
    $site = Site::factory()->create();

    $package = new PackageReadResult(
        archivePath: '',
        manifest: [],
        integrity: [],
        payload: [
            'pages/imported.json' => json_encode([
                'type' => 'page',
                'uuid' => (string) Str::uuid(),
                'id' => 123,
                'attributes' => [
                    'name' => 'Workspace imported page',
                    'layout_id' => $layout->getKey(),
                    'type_id' => $type->getKey(),
                    'site_id' => $site->getKey(),
                    'parent_id' => null,
                ],
                'owned_relations' => ['page_urls' => []],
                'shared_relations' => [
                    'layout' => ['ref' => 'layout:' . $layout->getKey()],
                    'type' => ['ref' => 'type:' . $type->getKey()],
                    'site' => ['ref' => 'site:' . $site->getKey()],
                ],
                'media_bindings' => [],
            ], JSON_THROW_ON_ERROR),
        ],
    );

    $map = new ResolutionMap(
        resolved: [
            'layout:' . $layout->getKey() => new MatchResolution(localId: (int) $layout->getKey(), strategy: 'key'),
            'type:' . $type->getKey() => new MatchResolution(localId: (int) $type->getKey(), strategy: 'key'),
            'site:' . $site->getKey() => new MatchResolution(localId: (int) $site->getKey(), strategy: 'slug'),
        ],
        unresolved: [],
    );

    $report = (new PageImportService(new PublishingStudioMigrationAssistantContextResolver))->import(
        $package,
        $map,
        $workspace->id,
    );

    $page = Page::query()->withoutGlobalScopes()->whereKey($report->createdPageIds[0])->firstOrFail();

    expect($report->isSuccess())->toBeTrue()
        ->and((int) $page->workspace_id)->toBe((int) $workspace->id);
});
