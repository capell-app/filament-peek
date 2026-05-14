<?php

declare(strict_types=1);

use Capell\Admin\Filament\Resources\Pages\PageResource;
use Capell\Admin\Filament\Resources\Pages\Pages\EditPage;
use Capell\Core\Models\Page;
use Capell\PublishingStudio\Actions\RecordPublishingRevisionAction;
use Capell\PublishingStudio\Enums\PublishingRevisionEventEnum;
use Capell\PublishingStudio\Filament\Resources\Pages\Pages\PageVersionHistoryPage;
use Capell\PublishingStudio\Models\PublishingRevision;
use Capell\Tests\Support\Concerns\CreatesAdminUser;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

uses(CreatesAdminUser::class)
    ->group('page');

beforeEach(function (): void {
    test()->actingAsAdmin();
});

test('renders successfully', function (): void {
    $page = Page::factory()->create();

    livewire(PageVersionHistoryPage::class, ['record' => $page->getRouteKey()])
        ->assertSuccessful();
});

test('accessible via the page resource url', function (): void {
    $page = Page::factory()->create();

    get(PageResource::getUrl('history', ['record' => $page]))
        ->assertSuccessful();
});

test('shows no published revisions prompt when page has no revision rows', function (): void {
    $page = Page::factory()->create();

    livewire(PageVersionHistoryPage::class, ['record' => $page->getRouteKey()])
        ->assertSee(__('capell-publishing-studio::workspace.revisions.empty'));
});

test('lists published revisions in the timeline', function (): void {
    $page = Page::factory()->create(['name' => 'Published page']);

    RecordPublishingRevisionAction::run(
        revisionableType: Page::class,
        revisionableId: (int) $page->getKey(),
        revisionableUuid: $page->uuid,
        eventType: PublishingRevisionEventEnum::Published,
        beforePayload: ['name' => 'Old page'],
        afterPayload: ['name' => 'Published page'],
        notes: 'release notes',
    );

    livewire(PageVersionHistoryPage::class, ['record' => $page->getRouteKey()])
        ->assertSee(__('capell-publishing-studio::workspace.revisions.version_label', ['version' => 1]))
        ->assertSee('release notes')
        ->assertDontSee(__('capell-publishing-studio::workspace.revisions.empty'));
});

test('auto-selects the latest published revision on mount', function (): void {
    $page = Page::factory()->create();

    $revision = RecordPublishingRevisionAction::run(
        revisionableType: Page::class,
        revisionableId: (int) $page->getKey(),
        revisionableUuid: $page->uuid,
        eventType: PublishingRevisionEventEnum::Published,
        beforePayload: ['name' => 'Old'],
        afterPayload: ['name' => 'New'],
    );

    livewire(PageVersionHistoryPage::class, ['record' => $page->getRouteKey()])
        ->assertSet('selectedRevisionId', $revision->id);
});

test('selectRevision updates the selected revision id', function (): void {
    $page = Page::factory()->create();

    $first = RecordPublishingRevisionAction::run(
        revisionableType: Page::class,
        revisionableId: (int) $page->getKey(),
        revisionableUuid: $page->uuid,
        eventType: PublishingRevisionEventEnum::Published,
        beforePayload: ['name' => 'First'],
        afterPayload: ['name' => 'Second'],
    );

    $second = RecordPublishingRevisionAction::run(
        revisionableType: Page::class,
        revisionableId: (int) $page->getKey(),
        revisionableUuid: $page->uuid,
        eventType: PublishingRevisionEventEnum::Restored,
        beforePayload: ['name' => 'Second'],
        afterPayload: ['name' => 'First'],
    );

    livewire(PageVersionHistoryPage::class, ['record' => $page->getRouteKey()])
        ->assertSet('selectedRevisionId', $second->id)
        ->call('selectRevision', $first->id)
        ->assertSet('selectedRevisionId', $first->id);
});

test('version history action exists on edit page with revision count in label', function (): void {
    $page = Page::factory()->create();

    RecordPublishingRevisionAction::run(
        revisionableType: Page::class,
        revisionableId: (int) $page->getKey(),
        revisionableUuid: $page->uuid,
        eventType: PublishingRevisionEventEnum::Published,
        beforePayload: ['name' => 'Old'],
        afterPayload: ['name' => 'New'],
    );

    livewire(EditPage::class, ['record' => $page->getRouteKey()])
        ->assertActionExists('revisions')
        ->assertActionHasLabel('revisions', __('capell-admin::button.revisions', [
            'count' => PublishingRevision::query()->where('revisionable_uuid', $page->uuid)->count(),
        ]));
});
