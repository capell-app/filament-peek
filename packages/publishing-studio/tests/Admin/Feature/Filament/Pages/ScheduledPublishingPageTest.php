<?php

declare(strict_types=1);

use Capell\Core\Models\Page;
use Capell\PublishingStudio\Actions\CancelSchedulerEventAction;
use Capell\PublishingStudio\Actions\DashboardReports\BuildContentSchedulerEventsAction;
use Capell\PublishingStudio\Actions\SyncWorkspaceSchedulerEventsAction;
use Capell\PublishingStudio\Enums\SchedulerEventStateEnum;
use Capell\PublishingStudio\Enums\SchedulerEventTypeEnum;
use Capell\PublishingStudio\Filament\Pages\ScheduledPublishingPage;
use Capell\PublishingStudio\Filament\Resources\PublishingStudio\WorkspaceResource;
use Capell\PublishingStudio\Models\SchedulerEvent;
use Capell\PublishingStudio\Models\Workspace;
use Capell\Tests\Support\Concerns\CreatesAdminUser;

use function Pest\Livewire\livewire;

uses(CreatesAdminUser::class);

beforeEach(function (): void {
    test()->actingAsAdmin();
});

test('lists page and workspace scheduler events', function (): void {
    Page::factory()->create([
        'visible_from' => now()->addDays(3),
        'name' => 'Spring sale',
    ]);
    Page::factory()->create([
        'visible_from' => now()->subMonth(),
        'visible_until' => now()->addWeek(),
        'name' => 'Holiday banner',
    ]);
    Page::factory()->create([
        'visible_from' => now()->subMonth(),
        'visible_until' => null,
        'name' => 'Always live info page',
    ]);
    Workspace::factory()->scheduled(now()->addDays(5))->create([
        'name' => 'Campaign workspace',
        'review_reminder_at' => now()->addDays(2),
    ]);

    livewire(ScheduledPublishingPage::class)
        ->assertSee('Spring sale')
        ->assertSee('Holiday banner')
        ->assertSee('Campaign workspace')
        ->assertSee('Review Reminder');

    expect(BuildContentSchedulerEventsAction::run()->pluck('title')->all())
        ->not->toContain('Always live info page')
        ->and(BuildContentSchedulerEventsAction::run(eventType: SchedulerEventTypeEnum::ReviewReminder)->pluck('title')->all())
        ->toContain('Campaign workspace');
});

test('uses content scheduler labels and prominent navigation', function (): void {
    expect(ScheduledPublishingPage::getSlug())->toBe('scheduled-publishing');
    expect(ScheduledPublishingPage::getNavigationLabel())->toBe('Content Scheduler')
        ->and(ScheduledPublishingPage::getNavigationGroup())->toBe((string) __('capell-admin::navigation.group_workflow'))
        ->and(ScheduledPublishingPage::getNavigationItems()[0]->getSort())->toBe(1);
});

test('shows a navigation badge for upcoming scheduler events', function (): void {
    Page::factory()->create([
        'visible_from' => now()->addDays(3),
        'name' => 'Spring sale',
    ]);

    expect(ScheduledPublishingPage::getNavigationBadge())->toBe('1');
});

test('searches scheduler rows from the array data source', function (): void {
    Page::factory()->create([
        'visible_from' => now()->addDays(3),
        'name' => 'Spring sale',
    ]);
    Workspace::factory()->scheduled(now()->addDays(5))->create([
        'name' => 'Campaign workspace',
    ]);

    livewire(ScheduledPublishingPage::class)
        ->searchTable('Campaign')
        ->assertSee('Campaign workspace')
        ->assertDontSee('Spring sale');
});

test('sorts scheduler rows from the array data source', function (): void {
    Page::factory()->create([
        'visible_from' => now()->addDays(3),
        'name' => 'Beta launch',
    ]);
    Workspace::factory()->scheduled(now()->addDays(5))->create([
        'name' => 'Alpha campaign',
    ]);

    livewire(ScheduledPublishingPage::class)
        ->sortTable('title')
        ->assertSeeInOrder(['Alpha campaign', 'Beta launch']);
});

test('filters scheduler rows by durable state', function (): void {
    Page::factory()->create([
        'visible_from' => now()->addDays(3),
        'name' => 'Scheduled page',
    ]);
    $workspace = Workspace::factory()->create(['name' => 'Failed campaign']);

    SchedulerEvent::query()->create([
        'event_type' => SchedulerEventTypeEnum::Publish,
        'state' => SchedulerEventStateEnum::Failed,
        'source_type' => $workspace->getMorphClass(),
        'source_id' => $workspace->id,
        'workspace_id' => $workspace->id,
        'scheduled_for' => now()->addDay(),
        'idempotency_key' => 'failed-campaign',
    ]);

    livewire(ScheduledPublishingPage::class)
        ->filterTable('state', SchedulerEventStateEnum::Failed->value)
        ->assertSee('Failed campaign')
        ->assertDontSee('Scheduled page');
});

test('cancel action clears the underlying workspace publish schedule', function (): void {
    $workspace = Workspace::factory()->scheduled(now()->addDays(5))->create([
        'name' => 'Cancelable campaign',
    ]);
    Page::factory()->create(['workspace_id' => $workspace->id]);
    SyncWorkspaceSchedulerEventsAction::run($workspace);
    $event = SchedulerEvent::query()
        ->where('workspace_id', $workspace->id)
        ->where('event_type', SchedulerEventTypeEnum::Publish->value)
        ->firstOrFail();

    CancelSchedulerEventAction::run($event);

    expect($workspace->fresh()->publish_at)->toBeNull()
        ->and($event->fresh()->state)->toBe(SchedulerEventStateEnum::Cancelled);
});

test('scheduled publishing-studio are visible from workspace resource queries', function (): void {
    $scheduled = Workspace::factory()->scheduled(now()->addDays(5))->create([
        'name' => 'Campaign workspace',
    ]);

    expect(WorkspaceResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($scheduled->id);
});
