<?php

declare(strict_types=1);

use Capell\Admin\Facades\CapellAdmin;
use Capell\Admin\Filament\Resources\PageResource\Pages\ListPages;
use Capell\Core\Models\Page;
use Capell\Tests\Support\Concerns\CreatesAdminUser;

use function Pest\Livewire\livewire;

uses(CreatesAdminUser::class)
    ->group('page');

beforeEach(function (): void {
    test()->actingAsAdmin();
});

test('can list pages', function (): void {
    CapellAdmin::addResourcePage('article', 'TestClass');

    // TODO figure out how to work a cusotm facotry article page
    Page::factory()->article()->create();

    $pages = Page::factory()->count(5)->create();

    livewire(ListPages::class)
        ->assertSuccessful()
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($pages);
});
