<?php

declare(strict_types=1);

use Capell\Core\Enums\LayoutEnum;
use Capell\Core\Models\Layout;
use Capell\Core\Support\Creator\LayoutCreator;

it('installs Foundation theme layout defaults without owning the home hero', function (): void {
    resolve(LayoutCreator::class)->setup();

    $homeLayout = Layout::query()->where('key', LayoutEnum::Home->value)->firstOrFail();
    $homeLayout->update(['containers' => [], 'widgets' => []]);
    $homeLayout->refresh();

    expect($homeLayout->containers)->not->toHaveKey('hero')
        ->and($homeLayout->widgets)->toBe([]);

    test()->artisan('capell:foundation-theme-setup')->assertSuccessful();

    $homeLayout->refresh();

    expect($homeLayout->containers)->toBe([])
        ->and($homeLayout->widgets)->toBe([]);
});

it('does not mutate home hero defaults on repeated setup', function (): void {
    resolve(LayoutCreator::class)->setup();

    Layout::query()
        ->where('key', LayoutEnum::Home->value)
        ->firstOrFail()
        ->update([
            'containers' => [
                'hero' => [
                    'widgets' => [
                        ['widget_key' => 'hero'],
                    ],
                ],
            ],
            'widgets' => ['hero'],
        ]);

    test()->artisan('capell:foundation-theme-setup')->assertSuccessful();
    test()->artisan('capell:foundation-theme-setup')->assertSuccessful();

    $homeLayout = Layout::query()->where('key', LayoutEnum::Home->value)->firstOrFail();

    expect($homeLayout->containers)->toHaveKey('hero')
        ->and($homeLayout->widgets)->toBe(['hero']);
});
