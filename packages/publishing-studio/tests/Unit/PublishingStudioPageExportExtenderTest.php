<?php

declare(strict_types=1);

use Capell\PublishingStudio\Enums\WorkspaceKindEnum;
use Capell\PublishingStudio\Enums\WorkspaceStatusEnum;
use Capell\PublishingStudio\Extenders\PublishingStudioPageExportExtender;
use Capell\PublishingStudio\Models\Workspace;
use Capell\Tests\Support\Concerns\CreatesAdminUser;
use Illuminate\Auth\Access\AuthorizationException;
use Spatie\Permission\Models\Permission;

uses(CreatesAdminUser::class);

beforeEach(function (): void {
    Permission::findOrCreate('ViewAny:Workspace', 'web');
    Permission::findOrCreate('View:Workspace', 'web');
});

it('labels export workspace options with identifying metadata', function (): void {
    $this->actingAs(test()->createUserWithPermission(['ViewAny:Workspace', 'View:Workspace']));

    $workspace = Workspace::factory()->create([
        'name' => 'Launch',
        'kind' => WorkspaceKindEnum::Release,
        'status' => WorkspaceStatusEnum::Approved,
    ]);

    $options = exportWorkspaceOptions(new PublishingStudioPageExportExtender);

    expect($options[$workspace->getKey()] ?? null)
        ->toContain('Launch')
        ->toContain(WorkspaceKindEnum::Release->getLabel())
        ->toContain(WorkspaceStatusEnum::Approved->getLabel());
});

it('uses prefix search for export workspace options', function (): void {
    $this->actingAs(test()->createUserWithPermission(['ViewAny:Workspace', 'View:Workspace']));

    $matchingWorkspace = Workspace::factory()->create(['name' => 'Alpha launch']);
    $nonMatchingWorkspace = Workspace::factory()->create(['name' => 'The Alpha launch']);

    $options = exportWorkspaceOptions(new PublishingStudioPageExportExtender, 'Alpha');

    expect($options)->toHaveKey($matchingWorkspace->getKey())
        ->and($options)->not->toHaveKey($nonMatchingWorkspace->getKey());
});

it('authorizes selected export workspace resolution', function (): void {
    $this->actingAs(test()->createUserWithPermission('ViewAny:Workspace'));

    $workspace = Workspace::factory()->create();

    expect(fn (): array => (new PublishingStudioPageExportExtender)->resolveOptions([
        'source_workspace_id' => $workspace->getKey(),
    ]))->toThrow(AuthorizationException::class);
});

/**
 * @return array<int|string, string>
 */
function exportWorkspaceOptions(PublishingStudioPageExportExtender $extender, ?string $search = null): array
{
    $method = new ReflectionMethod($extender, 'workspaceOptions');

    /** @var array<int|string, string> $options */
    $options = $method->invoke($extender, $search);

    return $options;
}
