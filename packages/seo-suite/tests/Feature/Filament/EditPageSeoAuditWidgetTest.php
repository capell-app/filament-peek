<?php

declare(strict_types=1);

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\Translation;
use Capell\SeoSuite\Filament\Widgets\EditPageSeoAuditWidget;
use Capell\SeoSuite\Support\Admin\PageSeoAuditPageEditExtender;
use Capell\Tests\Support\Concerns\CreatesAdminUser;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Livewire;

uses(CreatesAdminUser::class);

beforeEach(function (): void {
    test()->actingAsAdmin();
});

it('contributes the edit page seo audit widget from seo suite', function (): void {
    $extender = resolve(PageSeoAuditPageEditExtender::class);

    expect($extender->getHeaderWidgets())->toBe([EditPageSeoAuditWidget::class])
        ->and($extender->getFormActions())->toBe([]);
});

it('renders no checks when report context is unavailable', function (): void {
    Livewire::test(EditPageSeoAuditWidget::class)
        ->assertSeeText(__('capell-seo-suite::generic.no_checks'));
});

it('passes the meta description check when description is present', function (): void {
    $language = Language::factory()->create();
    $site = Site::factory()->recycle($language)->create();

    $page = Page::factory()
        ->state(['site_id' => $site->id])
        ->has(
            Translation::factory()->state([
                'language_id' => $language->id,
                'meta' => ['description' => 'A useful description for this page.', 'slug' => 'home'],
            ]),
            'translations',
        )
        ->create();

    Livewire::test(EditPageSeoAuditWidget::class, ['record' => $page])
        ->assertSet('checks', fn (SupportCollection $checks): bool => $checks['meta_description']->pass === true);
});
