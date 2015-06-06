<?php

declare(strict_types=1);

use Awcodes\Curator\CuratorServiceProvider;
use Capell\Admin\Filament\Resources\Pages\Pages\EditPage;
use Capell\Core\Models\Language;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\FilamentPeek\Actions\FindPagePreviewSnapshotAction;
use Capell\FilamentPeek\Data\PagePreviewSnapshotData;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Livewire\Livewire;

it('creates the unsaved preview snapshot when the header action is clicked', function (): void {
    $user = $this->createUserWithRole('super_admin');
    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->app->register(CuratorServiceProvider::class);
    $this->registerAndMigrateSettings(
        [
            '2026_05_10_190871_01_create_ai-orchestrator_settings',
            '2026_09_04_213000_01_encrypt_ai_orchestrator_api_key',
        ],
        __DIR__ . '/../../../ai-orchestrator/database/settings',
    );
    config()->set('settings.migrations_paths.capell-ai-orchestrator', __DIR__ . '/../../../ai-orchestrator/database/settings');

    $language = Language::factory()->create();
    $site = Site::factory()->withTranslations($language)->language($language)->create();
    $layout = Layout::factory()->site($site)->default()->create(['containers' => []]);
    $page = Page::factory()
        ->site($site)
        ->layout($layout)
        ->withTranslations($language, [
            'title' => 'Saved title',
            'content' => '<p>Saved body</p>',
        ])
        ->create(['name' => 'Saved page name']);

    $component = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
        ->set('data.translations.0.title', 'Unsaved preview title')
        ->callAction('peekPagePreview')
        ->assertDispatched('open-capell-page-preview-modal');

    $dispatches = $component->effects['dispatches'] ?? [];

    if (! is_array($dispatches)) {
        $dispatches = [];
    }

    $event = collect($dispatches)
        ->firstWhere('name', 'open-capell-page-preview-modal');

    throw_unless(is_array($event), RuntimeException::class, 'Expected the open-capell-page-preview-modal dispatch to be an array.');

    $params = $event['params'] ?? null;

    throw_unless(is_array($params), RuntimeException::class, 'Expected the dispatched event to carry a params array.');

    $iframeUrl = $params['iframeUrl'] ?? null;

    expect($iframeUrl)->toBeString()
        ->and($iframeUrl)->toContain('/capell-filament-peek/preview/')
        ->and($params['modalTitle'] ?? null)->toBe('Preview changes')
        ->and($params['scopeLabel'] ?? null)->toBe('Unsaved changes - not published')
        ->and($params['subjectLabel'] ?? null)->toBe('Unsaved preview title')
        ->and($params['ttlLabel'] ?? null)->toBe('This preview link stays valid for 15 minutes.')
        ->and($params['livewireId'] ?? null)->toBe($component->id());

    expect(config('capell-filament-peek.preview.modal_device_presets.mobile.width'))->toBe('390px')
        ->and(config('capell-filament-peek.preview.modal_device_presets.tablet.rotatable'))->toBeTrue()
        ->and(config('capell-filament-peek.preview.modal_initial_device_preset'))->toBe('desktop');

    $token = Str::between((string) $iframeUrl, '/capell-filament-peek/preview/', '?');
    $snapshot = FindPagePreviewSnapshotAction::run($token);

    throw_unless($snapshot instanceof PagePreviewSnapshotData, RuntimeException::class, 'Expected page preview snapshot to be stored.');

    expect($snapshot)->not->toBeNull()
        ->and($snapshot->formState['name'])->toBe('Unsaved preview title')
        ->and($snapshot->formState['translations'][0]['title'])->toBe('Unsaved preview title');

    $page->refresh();

    expect($page->name)->toBe('Saved page name')
        ->and($page->translation->title)->toBe('Saved title');
});
