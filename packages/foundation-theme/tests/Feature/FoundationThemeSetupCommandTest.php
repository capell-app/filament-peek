<?php

declare(strict_types=1);

use Capell\Core\Enums\LayoutEnum;
use Capell\Core\Models\Layout;
use Capell\Core\Support\Creator\LayoutCreator;

it('installs Foundation theme layout defaults without owning the home hero', function (): void {
    $homeLayout = resolve(LayoutCreator::class)->createHomeLayout();
    $homeLayout->update(['containers' => [], 'blocks' => []]);
    $homeLayout->refresh();

    expect($homeLayout->containers)->not->toHaveKey('hero')
        ->and($homeLayout->blocks)->toBe([])
        ->and(Layout::query()->where('key', LayoutEnum::Results->value)->exists())->toBeFalse();

    test()->artisan('capell:foundation-theme-setup')->assertSuccessful();

    $homeLayout->refresh();

    expect($homeLayout->containers)->not->toHaveKey('hero')
        ->and($homeLayout->containers)->toHaveKey('main')
        ->and($homeLayout->containers['main']['meta']['colspan'])->toBe(12)
        ->and($homeLayout->containers['main']['blocks'])->toBe([
            ['block_key' => 'page-content'],
        ])
        ->and($homeLayout->blocks)->toBe(['page-content'])
        ->and(Layout::query()->where('key', LayoutEnum::Results->value)->exists())->toBeFalse();
});

it('keeps home page content defaults stable on repeated setup', function (): void {
    resolve(LayoutCreator::class)
        ->createHomeLayout()
        ->update([
            'containers' => [
                'hero' => [
                    'blocks' => [
                        ['block_key' => 'hero'],
                    ],
                ],
            ],
            'blocks' => ['hero'],
        ]);

    test()->artisan('capell:foundation-theme-setup')->assertSuccessful();
    test()->artisan('capell:foundation-theme-setup')->assertSuccessful();

    $homeLayout = Layout::query()->where('key', LayoutEnum::Home->value)->firstOrFail();

    expect($homeLayout->containers)->not->toHaveKey('hero')
        ->and($homeLayout->containers)->toHaveKey('main')
        ->and($homeLayout->containers['main']['meta']['colspan'])->toBe(12)
        ->and($homeLayout->containers['main']['blocks'])->toBe([
            ['block_key' => 'page-content'],
        ])
        ->and($homeLayout->blocks)->toBe(['page-content']);
});
