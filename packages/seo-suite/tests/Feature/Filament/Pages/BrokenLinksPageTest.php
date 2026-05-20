<?php

declare(strict_types=1);

use Capell\Core\Models\Page;
use Capell\SeoSuite\Filament\Pages\BrokenLinksPage;
use Capell\SeoSuite\Models\BrokenLink;
use Capell\Tests\Support\Concerns\CreatesAdminUser;

use function Pest\Livewire\livewire;

use Spatie\Permission\Models\Permission;

uses(CreatesAdminUser::class);

beforeEach(function (): void {
    Permission::query()->firstOrCreate(['name' => 'View:BrokenLinksPage', 'guard_name' => 'web']);

    test()->actingAsAdmin();
    auth()->user()->givePermissionTo('View:BrokenLinksPage');
});

it('renders broken link rows with related page names', function (): void {
    $page = Page::factory()
        ->withTranslations(data: ['title' => 'Broken Link Host Page'])
        ->create(['name' => 'Broken Link Host Page']);

    BrokenLink::query()->create([
        'page_id' => $page->getKey(),
        'target_url' => 'https://example.test/missing-resource',
        'http_status' => 404,
        'last_checked_at' => now(),
    ]);

    livewire(BrokenLinksPage::class)
        ->assertSuccessful()
        ->assertSee('Broken Link Host Page')
        ->assertSee('https://example.test/missing-resource')
        ->assertSee('404');
});
