<?php

declare(strict_types=1);

use Capell\Core\Models\Media;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Hero\Actions\CreateHeroWidgetAction;
use Capell\Hero\Enums\WidgetComponentEnum;
use Capell\Layout\Database\Factories\LayoutFactory;
use Capell\Layout\Models\Widget;
use Capell\Layout\Models\WidgetAsset;
use Capell\Tests\Fixtures\Support\Concerns\TestingFrontend;
use Illuminate\Support\Facades\Storage;
use Pest\Expectation;

use function Pest\Laravel\get;

use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\BaseAssert;

uses(TestingFrontend::class);

it('creates hero widget with expected meta', function (): void {
    $widget = CreateHeroWidgetAction::run();
    WidgetAsset::factory()->count(3)->widget($widget)->create();

    expect($widget)
        ->toBeInstanceOf(Widget::class)
        ->key->toBe('hero')
        ->meta->scoped(
            fn (Expectation $expectation) => $expectation->component->toBe(WidgetComponentEnum::Hero->value),
        )
        ->assets->toHaveCount(3);
});

it('renders hero widget with page hero content', function (): void {
    $site = Site::factory()->withTranslations()->create();
    $widget = CreateHeroWidgetAction::run();
    $layout = (new LayoutFactory)->widgets([$widget])->create();
    $heroContent = collect(fake()->paragraphs(2));
    $page = Page::factory()
        ->site($site)
        ->layout($layout)
        ->withTranslations(
            data: [
                'meta' => [
                    'hero' => $heroContent->map(fn (string $paragraph): string => sprintf('<p>%s</p>', $paragraph))
                        ->implode("\n"),
                ],
            ],
        )
        ->create();

    get($page->pageUrl->full_url)
        ->assertOk()
        ->assertElementExists(
            '.widget-hero',
            fn (AssertElement $element): BaseAssert => $element->contains('.hero-item', 1)
                ->find(
                    '.hero-content',
                    fn (AssertElement $content): BaseAssert => $content->each(
                        'p',
                        fn (AssertElement $element, int $index): BaseAssert => $element->containsText($heroContent[$index]),
                    ),
                ),
        );
});

it('renders hero widget with assets', function (callable $factory, string $mediaRelation, callable $srcResolver): void {
    $site = Site::factory()->withTranslations()->create();
    $widget = CreateHeroWidgetAction::run();
    $layout = (new LayoutFactory)->widgets([$widget])->create();
    $factory($widget)->create();
    $page = Page::factory()->site($site)->layout($layout)->withTranslations()->create();
    $widgetAssets = $widget->widgetAssets()
        ->ordered()
        ->with([
            'asset.type',
            'asset.translation',
            $mediaRelation,
        ])
        ->get();

    // Ensure all referenced media files exist on the fake disk
    $exampleImagePath = __DIR__ . '/../../../../Fixtures/Support/Files/Images/img.png';
    $exampleImage = file_get_contents($exampleImagePath);
    foreach ($widgetAssets as $widgetAsset) {
        $mediaCollection = data_get($widgetAsset, $mediaRelation);
        foreach ($mediaCollection as $media) {
            Storage::disk('public')->put($media->getPathRelativeToRoot(), $exampleImage);
        }
    }

    get($page->pageUrl->full_url)
        ->assertOk()
        ->assertElementExists(
            '.widget-hero',
            fn (AssertElement $element): BaseAssert => $element->contains('.hero-item', 3)
                ->contains('.hero-heading', 3)
                ->contains('h1.hero-heading', 1)
                ->contains('h2.hero-heading', 2)
                ->each(
                    '.hero-item .hero-content',
                    fn (AssertElement $content, int $index): BaseAssert => $content->containsText(
                        $widgetAssets[$index]->asset->translation->title,
                    )
                        ->find(
                            'img',
                            fn (AssertElement $imgElm): BaseAssert => $imgElm->has('alt', $widgetAssets[$index]->asset->translation->title),
                        ),
                ),
            // title, content, url
            // image src+alt
        );
})->with(
    [
        'widgetAssetHasMedia' => [
            fn (Widget $widget) => WidgetAsset::factory()->count(3)
                ->widget($widget)
                ->has(Media::factory()->image(), 'media'),
            'media',
        ],
        'assetHavingMedia' => [
            fn (Widget $widget) => WidgetAsset::factory()->count(3)
                ->widget($widget)
                ->assetHavingMedia(),
            'asset.media',
        ],
    ],
);

it('empty hero widget hidden', function (): void {
    $site = Site::factory()->withTranslations()->create();
    $widget = CreateHeroWidgetAction::run();
    $layout = (new LayoutFactory)->widgets([$widget])->create();
    $page = Page::factory()->site($site)->layout($layout)->withTranslations()->create();

    get($page->pageUrl->full_url)
        ->assertOk()
        ->assertDoesntExist('.widget-hero');
});
