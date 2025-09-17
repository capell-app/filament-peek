<?php

declare(strict_types=1);

use Capell\Core\Models\Page;
use Capell\Layout\Filament\Resources\Contents\Pages\EditContent;
use Capell\Layout\Filament\Resources\Contents\RelationManagers\PagesRelationManager;
use Capell\Layout\Models\Content;
use Capell\Layout\Models\Widget;
use Capell\Layout\Models\WidgetAsset;

use function Pest\Livewire\livewire;

it('can list pages for a content model', function (): void {
    $page = Page::factory()->create();
    $content = Content::factory()->create();

    Widget::factory()
        ->has(
            WidgetAsset::factory([
                'asset_type' => 'content',
                'asset_id' => $content->id,
                'page_id' => $page->id,
                'container' => 'main',
            ])
                ->sequence(
                    ['occurrence' => 1],
                    ['occurrence' => 2],
                )
                ->count(2),
            'assets'
        )
        ->create();

    $page = $content->pages->first();

    livewire(PagesRelationManager::class, [
        'ownerRecord' => $content,
        'pageClass' => EditContent::class,
    ])
        ->assertSuccessful()
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords($content->pages)
        ->assertTableColumnStateSet('name', [$page->name], record: $page);
});

it('can search pages for a content model', function (): void {
    $page = Page::factory()->create();
    $content = Content::factory()->create();
    Widget::factory()
        ->has(
            WidgetAsset::factory([
                'asset_type' => 'content',
                'asset_id' => $content->id,
                'page_id' => $page->id,
                'container' => 'main',
            ])
                ->sequence(
                    ['occurrence' => 1],
                    ['occurrence' => 2],
                    ['occurrence' => 3],
                    ['occurrence' => 4],
                    ['occurrence' => 5],
                )
                ->count(5),
            'assets'
        )
        ->create();

    $page = $content->pages->random();

    livewire(PagesRelationManager::class, [
        'ownerRecord' => $content,
        'pageClass' => EditContent::class,
    ])
        ->assertSuccessful()
        ->searchTable($page->getKey())
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords([$page]);
});
