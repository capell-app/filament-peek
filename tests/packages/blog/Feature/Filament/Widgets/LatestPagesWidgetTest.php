<?php

declare(strict_types=1);

use Capell\Admin\Filament\Widgets\LatestPagesWidget;
use Capell\Core\Models\Page;
use Capell\Tests\Support\Concerns\CreatesAdminUser;

use function Pest\Livewire\livewire;

uses(CreatesAdminUser::class)
    ->group('page');

it('renders the pages widget', function (): void {
    test()->actingAsAdmin();

    Page::factory(5)->create();

    ArticlePage::factory(5)->article()->create();

    livewire(LatestPagesWidget::class)
        ->assertOk()
        ->assertCountTableRecords(10);
});
