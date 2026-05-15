<?php

declare(strict_types=1);

use Capell\Core\Enums\LayoutEnum;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Core\Support\Creator\LayoutCreator;
use Capell\LayoutBuilder\Models\Element;

it('installs compact natural home hero defaults', function (): void {
    resolve(LayoutCreator::class)->setup();

    $homeLayout = Layout::query()
        ->where('key', LayoutEnum::Home->value)
        ->firstOrFail();

    $homeLayout->update(['containers' => [], 'elements' => []]);

    Page::factory()
        ->layout($homeLayout)
        ->withTranslations(data: [
            'content' => '<p>Welcome to Capell</p>',
            'meta' => ['hero' => '<p>Welcome to Capell</p>'],
        ])
        ->create(['name' => 'Home']);

    Element::query()->where('key', 'hero')->delete();

    test()->artisan('capell:hero-setup')->assertSuccessful();

    $homeLayout = Layout::query()->where('key', LayoutEnum::Home->value)->firstOrFail();
    $heroElement = Element::query()->where('key', 'hero')->firstOrFail();

    expect(array_keys($homeLayout->containers))->toBe(['hero', 'main'])
        ->and($homeLayout->containers['hero']['elements'])->toBe([
            ['element_key' => 'hero'],
        ])
        ->and($homeLayout->elements)->toBe(['hero', 'page-content'])
        ->and($heroElement->getMeta('height'))->toBe('small')
        ->and($heroElement->getMeta('color'))->toBe('light')
        ->and($heroElement->getMeta('content_align'))->toBe('center')
        ->and($heroElement->getMeta('content_width'))->toBe('balanced')
        ->and($heroElement->getMeta('media_position'))->toBe('right');

    $homePage = Page::query()
        ->where('layout_id', $homeLayout->id)
        ->with('translation')
        ->firstOrFail();

    expect($homePage->translation->getMeta('hero_title'))->toBe('Start with a clean foundation.')
        ->and($homePage->translation->getMeta('hero'))->toBe('<p>Shape this page around your content, navigation, and publishing workflow.</p>')
        ->and($homePage->translation->content)->toBe('<p>Add the most important details for this page here. Keep it concise, useful, and easy to scan.</p>');
});

it('does not duplicate hero defaults on repeated setup', function (): void {
    resolve(LayoutCreator::class)->setup();

    Layout::query()
        ->where('key', LayoutEnum::Home->value)
        ->firstOrFail()
        ->update(['containers' => [], 'elements' => []]);

    Element::query()->where('key', 'hero')->delete();

    test()->artisan('capell:hero-setup')->assertSuccessful();
    test()->artisan('capell:hero-setup')->assertSuccessful();

    $homeLayout = Layout::query()->where('key', LayoutEnum::Home->value)->firstOrFail();

    expect(array_keys($homeLayout->containers))->toBe(['hero', 'main'])
        ->and($homeLayout->elements)->toBe(['hero', 'page-content'])
        ->and(Element::query()->where('key', 'hero')->count())->toBe(1);
});
