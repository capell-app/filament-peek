<?php

declare(strict_types=1);

use Capell\PublishingStudio\Enums\WorkspaceKindEnum;
use Capell\PublishingStudio\Filament\Resources\PublishingStudio\Pages\ManagePublishingStudio;
use Capell\PublishingStudio\Models\Workspace;
use Capell\Tests\Support\Concerns\CreatesAdminUser;

use function Pest\Livewire\livewire;

uses(CreatesAdminUser::class);

it('creates release workspaces from the admin table action', function (): void {
    $this->actingAsAdmin();

    livewire(ManagePublishingStudio::class)
        ->callAction('createReleaseWorkspace', data: [
            'name' => 'Spring campaign release',
            'slug' => 'spring-campaign-release',
            'description' => 'Coordinated campaign changes.',
            'color' => '#1d4ed8',
            'settings' => [
                'required_approval_levels' => 2,
            ],
        ])
        ->assertHasNoActionErrors();

    $workspace = Workspace::query()
        ->where('slug', 'spring-campaign-release')
        ->firstOrFail();

    expect($workspace->kind)->toBe(WorkspaceKindEnum::Release);
});
