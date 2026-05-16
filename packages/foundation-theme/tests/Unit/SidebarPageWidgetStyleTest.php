<?php

declare(strict_types=1);

use Capell\Core\Database\Factories\MediaFactory;
use Capell\Core\Enums\MediaCollectionEnum;
use Capell\Core\Models\Language;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\FoundationTheme\Actions\BuildAssetBannerItemsAction;
use Capell\FoundationTheme\Actions\BuildBannerImageRenderDataAction;
use Capell\FoundationTheme\Actions\ResolveLoadedWidgetBackgroundImageAction;
use Capell\FoundationTheme\Livewire\Widget\AbstractWidget as LivewireWidget;
use Capell\FoundationTheme\View\Components\Widget\Page\AbstractPagesWidget;
use Capell\FoundationTheme\View\Components\Widget\Page\Breadcrumbs as BreadcrumbsWidget;
use Capell\FoundationTheme\View\Components\Widget\Page\Content as ContentWidget;
use Capell\Frontend\Data\FrontendContext;
use Capell\Frontend\Facades\Frontend;
use Capell\Frontend\Support\CapellFrontendContext;
use Capell\LayoutBuilder\Models\Element;
use Capell\LayoutBuilder\Models\ElementAsset;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Blaze\Blaze;

test('sidebar page widgets expose stable styling and current page hooks', function (): void {
    $component = new class(container: [], containerKey: 'sidebar', widgetIndex: 0, loop: (object) ['index' => 0], widget: new Element(['key' => 'pages', 'name' => 'Pages', 'meta' => ['view_file' => 'capell::components.no-results']])) extends AbstractPagesWidget
    {
        protected function mountWidget(): void
        {
            $this->pages = new Collection([
                ['title' => 'Current page', 'url' => '/current'],
            ]);
        }
    };

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($view->getData())->toHaveKey('pages')
        ->and($view->getData()['pages'])->toHaveCount(1)
        ->and($view->getData()['containerKey'])->toBe('sidebar');
});

test('banner image render data uses only preloaded element media', function (): void {
    $media = MediaFactory::new()->make([
        'collection_name' => MediaCollectionEnum::BackgroundImage->value,
    ]);
    $widget = new Element(['key' => 'banner', 'meta' => []]);
    $widget->setRelation('media', new Collection([$media]));

    $renderData = BuildBannerImageRenderDataAction::run($widget, null, null, false, false);

    expect($renderData->backgroundImage)->toBe($media);
});

test('banner image render data does not lazy-load element media', function (): void {
    $widget = Element::factory()->create(['key' => 'banner', 'meta' => []]);

    DB::enableQueryLog();

    $renderData = BuildBannerImageRenderDataAction::run($widget, null, null, false, false);

    expect($renderData->backgroundImage)->toBeNull()
        ->and(DB::getQueryLog())->toBe([]);

    DB::disableQueryLog();
});

test('widget wrapper background image resolution does not lazy-load media', function (): void {
    $widget = Element::factory()->create(['key' => 'section', 'meta' => []]);

    DB::enableQueryLog();

    $backgroundImage = ResolveLoadedWidgetBackgroundImageAction::run($widget);

    expect($backgroundImage)->toBeNull()
        ->and(DB::getQueryLog())->toBe([]);

    DB::disableQueryLog();
});

test('banner image content stays in normal flow when no background image exists', function (): void {
    $view = file_get_contents(dirname(__DIR__, 2) . '/resources/views/layout-builder/components/widget/banner-image.blade.php');

    expect($view)
        ->toContain("'absolute inset-0 flex items-end' => \$backgroundImage")
        ->toContain("'relative flex flex-col' => ! \$backgroundImage")
        ->toContain("'md:w-1/2' => \$backgroundImage")
        ->toContain("'md:pl-10' => \$backgroundImage && \$reverseOrder")
        ->toContain("'md:pr-10' => \$backgroundImage && ! \$reverseOrder");
});

test('asset banner slides use readable foregrounds without an image', function (): void {
    $view = file_get_contents(dirname(__DIR__, 2) . '/resources/views/layout-builder/components/widget/asset/banners.blade.php');

    expect($view)
        ->toContain("'bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-50' => ! \$hasImage")
        ->toContain("'text-gray-900 dark:text-gray-50' => ! \$hasImage")
        ->toContain("'text-gray-700 dark:text-gray-200' => ! \$hasImage")
        ->toContain('--swiper-pagination-bullet-inactive-color: #6b7280')
        ->toContain('bg-white/85')
        ->not->toContain('dark:bg-gray-900/85');
});

test('asset banner render data uses only loaded relations', function (): void {
    $media = MediaFactory::new()->make([
        'collection_name' => MediaCollectionEnum::Image->value,
    ]);
    $widgetAsset = ElementAsset::factory()->make([
        'asset_type' => Element::class,
    ]);
    $widgetAsset->setRelation('media', new Collection([$media]));

    $widget = new Element(['key' => 'asset-banners', 'meta' => []]);
    $widget->setRelation('assets', new Collection([$widgetAsset]));

    $items = BuildAssetBannerItemsAction::run($widget);

    expect($items)->toHaveCount(1)
        ->and($items->first()->image)->toBe($media);
});

test('asset banner render data uses linked page loaded on the asset model', function (): void {
    $linkedPage = new Page;
    $linkedPage->setRelation('pageUrl', (object) ['full_url' => '/linked-page']);
    $linkedPage->setRelation('translation', (object) ['link_text' => 'Read more']);

    $asset = new Element(['key' => 'linked-asset']);
    $asset->setRelation('linkedPage', $linkedPage);

    $widgetAsset = ElementAsset::factory()->make([
        'asset_type' => Element::class,
    ]);
    $widgetAsset->setRelation('asset', $asset);

    $widget = new Element(['key' => 'asset-banners', 'meta' => []]);
    $widget->setRelation('assets', new Collection([$widgetAsset]));

    $items = BuildAssetBannerItemsAction::run($widget);

    expect($items)->toHaveCount(1)
        ->and($items->first()->url)->toBe('/linked-page')
        ->and($items->first()->linkText)->toBe('Read more');
});

test('asset banner render data does not lazy-load relations', function (): void {
    $widget = Element::factory()->create(['key' => 'asset-banners', 'meta' => []]);

    DB::enableQueryLog();

    $items = BuildAssetBannerItemsAction::run($widget);

    expect($items)->toHaveCount(0)
        ->and(DB::getQueryLog())->toBe([]);

    DB::disableQueryLog();
});

test('public livewire widgets expose only an opaque reference as public state', function (): void {
    $reflection = new ReflectionClass(LivewireWidget::class);
    $publicProperties = collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
        ->map(fn (ReflectionProperty $property): string => $property->getName())
        ->all();

    expect($publicProperties)
        ->toContain('widgetReference')
        ->not->toContain('container')
        ->not->toContain('widget')
        ->not->toContain('widgetData')
        ->not->toContain('loop');
});

test('layout livewire widgets preserve extension widget data mount parameters', function (): void {
    $view = file_get_contents(dirname(__DIR__, 2) . '/resources/views/layout-builder/components/layout/widget.blade.php');

    expect($view)
        ->toContain("'widgetReference' => \$widgetReference")
        ->toContain("'widget_data' => \$widgetData")
        ->not->toContain("'widgetData' => \$widgetData");
});

test('public livewire widgets resolve the scoped layout element clone', function (): void {
    $language = Language::factory()->create();
    $site = Site::factory()->create(['language_id' => $language->getKey()]);
    $element = Element::factory()->create(['key' => 'featured-pages']);
    $firstOccurrenceAsset = Page::factory()->site($site)->withTranslations($language)->create(['name' => 'First occurrence']);
    $secondOccurrenceAsset = Page::factory()->site($site)->withTranslations($language)->create(['name' => 'Second occurrence']);
    $layout = Layout::factory()->site($site)->create([
        'containers' => [
            'main' => [
                'elements' => [
                    ['element_key' => $element->key, 'occurrence' => 1],
                    ['element_key' => $element->key, 'occurrence' => 2, 'meta' => ['show_page_title' => true]],
                ],
            ],
        ],
    ]);
    $page = Page::factory()->site($site)->layout($layout)->withTranslations($language)->create();

    ElementAsset::factory()
        ->element($element)
        ->asset($firstOccurrenceAsset)
        ->page($page, 'main', 1)
        ->create();

    ElementAsset::factory()
        ->element($element)
        ->asset($secondOccurrenceAsset)
        ->page($page, 'main', 2)
        ->create();

    app()->instance(CapellFrontendContext::class, new CapellFrontendContext(new FrontendContext(
        site: $site,
        language: $language,
        page: $page,
        layout: $layout,
        theme: null,
        params: [],
        slug: null,
    )));

    $component = new class extends LivewireWidget
    {
        /** @var list<int> */
        public array $assetIds = [];

        protected function mountWidget(): void
        {
            $this->assetIds = $this->widget->assets
                ->pluck('asset_id')
                ->map(fn (mixed $assetId): int => (int) $assetId)
                ->values()
                ->all();
        }
    };

    $component->mount(Crypt::encryptString(json_encode([
        'container_key' => 'main',
        'element_key' => $element->key,
        'language_id' => $language->getKey(),
        'layout_id' => $layout->getKey(),
        'occurrence' => 2,
        'page_id' => $page->getKey(),
        'page_type' => $page->getMorphClass(),
        'site_id' => $site->getKey(),
        'widget_index' => 1,
    ], JSON_THROW_ON_ERROR)));

    expect($component->assetIds)->toBe([(int) $secondOccurrenceAsset->getKey()]);

    $renderData = $component->render()->getData();

    expect($renderData['container'])->toHaveKey('elements')
        ->and($renderData['widgetData']['meta']['show_page_title'])->toBeTrue()
        ->and($renderData['widgetData']['occurrence'])->toBe(2);

    app()->instance(CapellFrontendContext::class, new CapellFrontendContext(new FrontendContext(
        site: null,
        language: null,
        page: null,
        layout: null,
        theme: null,
        params: [],
        slug: null,
    )));
    Frontend::clearResolvedInstance(CapellFrontendContext::class);

    $hydratedComponent = new class extends LivewireWidget
    {
        protected function mountWidget(): void {}
    };

    $hydratedComponent->mount(Crypt::encryptString(json_encode([
        'container_key' => 'main',
        'element_key' => $element->key,
        'language_id' => $language->getKey(),
        'layout_id' => $layout->getKey(),
        'occurrence' => 2,
        'page_id' => $page->getKey(),
        'page_type' => $page->getMorphClass(),
        'site_id' => $site->getKey(),
        'widget_data' => [
            'meta' => ['show_page_title' => true],
            'tracking_key' => 'kept-in-reference',
        ],
        'widget_index' => 1,
    ], JSON_THROW_ON_ERROR)));
    $hydratedData = $hydratedComponent->render()->getData();

    expect($hydratedData['widgetData']['tracking_key'])->toBe('kept-in-reference')
        ->and($hydratedData['widgetData']['meta']['show_page_title'])->toBeTrue();
});

test('public livewire widgets reject references without scoped page and site ids', function (): void {
    $language = Language::factory()->create();
    $site = Site::factory()->create(['language_id' => $language->getKey()]);
    $element = Element::factory()->create(['key' => 'legacy-featured-pages']);
    $layout = Layout::factory()->site($site)->create([
        'containers' => [
            'main' => [
                'elements' => [
                    ['element_key' => $element->key, 'occurrence' => 1],
                ],
            ],
        ],
    ]);

    $component = new class extends LivewireWidget
    {
        protected function mountWidget(): void
        {
            $this->widget;
        }
    };

    expect(fn (): null => $component->mount(Crypt::encryptString(json_encode([
        'container_key' => 'main',
        'element_key' => $element->key,
        'language_id' => $language->getKey(),
        'layout_id' => $layout->getKey(),
        'occurrence' => 1,
        'widget_index' => 0,
    ], JSON_THROW_ON_ERROR))))
        ->toThrow(Exception::class, 'Widget reference is invalid');
});

test('public livewire widgets can hydrate widgets from global layouts', function (): void {
    $language = Language::factory()->create();
    $site = Site::factory()->create(['language_id' => $language->getKey()]);
    $element = Element::factory()->create(['key' => 'global-featured-pages']);
    $layout = Layout::factory()->create([
        'site_id' => null,
        'containers' => [
            'main' => [
                'elements' => [
                    [
                        'element_key' => $element->key,
                        'occurrence' => 1,
                        'meta' => [
                            'page_content' => ['title', 'content'],
                            'show_page_title' => true,
                        ],
                    ],
                ],
            ],
        ],
    ]);
    $page = Page::factory()->site($site)->layout($layout)->withTranslations($language)->create();

    app()->instance(CapellFrontendContext::class, new CapellFrontendContext(new FrontendContext(
        site: $site,
        language: $language,
        page: $page,
        layout: null,
        theme: null,
        params: [],
        slug: null,
    )));
    Frontend::clearResolvedInstance(CapellFrontendContext::class);

    $component = new class extends LivewireWidget
    {
        public string $resolvedKey = '';

        protected function mountWidget(): void
        {
            $this->resolvedKey = $this->widget->key;
        }
    };

    $component->mount(Crypt::encryptString(json_encode([
        'container_key' => 'main',
        'element_key' => $element->key,
        'language_id' => $language->getKey(),
        'layout_id' => $layout->getKey(),
        'occurrence' => 1,
        'page_id' => $page->getKey(),
        'page_type' => $page->getMorphClass(),
        'site_id' => $site->getKey(),
        'widget_index' => 0,
    ], JSON_THROW_ON_ERROR)));

    expect($component->resolvedKey)->toBe('global-featured-pages');
});

test('public livewire widgets reject global layout references replayed under another site', function (): void {
    $language = Language::factory()->create();
    $referenceSite = Site::factory()->create(['language_id' => $language->getKey()]);
    $currentSite = Site::factory()->create(['language_id' => $language->getKey()]);
    $element = Element::factory()->create(['key' => 'global-cross-site-pages']);
    $layout = Layout::factory()->create([
        'site_id' => null,
        'containers' => [
            'main' => [
                'elements' => [
                    ['element_key' => $element->key, 'occurrence' => 1],
                ],
            ],
        ],
    ]);
    $referencePage = Page::factory()->site($referenceSite)->layout($layout)->withTranslations($language)->create();
    $currentPage = Page::factory()->site($currentSite)->layout($layout)->withTranslations($language)->create();

    app()->instance(CapellFrontendContext::class, new CapellFrontendContext(new FrontendContext(
        site: $currentSite,
        language: $language,
        page: $currentPage,
        layout: null,
        theme: null,
        params: [],
        slug: null,
    )));
    Frontend::clearResolvedInstance(CapellFrontendContext::class);

    $component = new class extends LivewireWidget
    {
        protected function mountWidget(): void
        {
            $this->widget;
        }
    };

    expect(fn (): null => $component->mount(Crypt::encryptString(json_encode([
        'container_key' => 'main',
        'element_key' => $element->key,
        'language_id' => $language->getKey(),
        'layout_id' => $layout->getKey(),
        'occurrence' => 1,
        'page_id' => $referencePage->getKey(),
        'page_type' => $referencePage->getMorphClass(),
        'site_id' => $referenceSite->getKey(),
        'widget_index' => 0,
    ], JSON_THROW_ON_ERROR))))
        ->toThrow(Exception::class, 'Element not found');
});

test('public livewire page content widgets render from encrypted context without ambient frontend state', function (): void {
    $language = Language::factory()->create();
    $site = Site::factory()->create(['language_id' => $language->getKey()]);
    $element = Element::factory()->create([
        'key' => 'page-content',
        'meta' => ['view_file' => 'capell-layout-builder::components.widget.page.content'],
    ]);
    $layout = Layout::factory()->site($site)->create([
        'containers' => [
            'main' => [
                'elements' => [
                    ['element_key' => $element->key, 'occurrence' => 1],
                ],
            ],
        ],
    ]);
    $page = Page::factory()->site($site)->layout($layout)->withTranslations($language, [
        'title' => 'Hydrated page title',
        'content' => '<p>Hydrated page content</p>',
    ])->create();

    app()->instance(CapellFrontendContext::class, new CapellFrontendContext(new FrontendContext(
        site: null,
        language: null,
        page: null,
        layout: null,
        theme: null,
        params: [],
        slug: null,
    )));
    Frontend::clearResolvedInstance(CapellFrontendContext::class);

    $component = new class extends LivewireWidget
    {
        protected static string $defaultView = 'capell-layout-builder::components.widget.page.content';

        protected function mountWidget(): void {}
    };

    $component->mount(Crypt::encryptString(json_encode([
        'container_key' => 'main',
        'element_key' => $element->key,
        'language_id' => $language->getKey(),
        'layout_id' => $layout->getKey(),
        'occurrence' => 1,
        'page_id' => $page->getKey(),
        'page_type' => $page->getMorphClass(),
        'site_id' => $site->getKey(),
        'widget_data' => [
            'meta' => [
                'page_content' => ['title', 'content'],
                'show_page_title' => true,
            ],
        ],
        'widget_index' => 0,
    ], JSON_THROW_ON_ERROR)));

    $view = $component->render();
    $renderData = $view->getData();
    $renderedPage = $renderData['pageRecord'];
    $wasBlazeEnabled = Blaze::isEnabled();
    Blaze::disable();

    try {
        $html = $view->render();
    } finally {
        if ($wasBlazeEnabled) {
            Blaze::enable();
        }
    }

    expect($renderedPage->translation->title)->toBe('Hydrated page title')
        ->and($renderedPage->translation->content)->toBe('<p>Hydrated page content</p>')
        ->and($renderData['widgetData']['meta']['page_content'])->toBe(['title', 'content'])
        ->and($html)->toContain('Hydrated page title')
        ->and($html)->toContain('Hydrated page content')
        ->and(file_get_contents(dirname(__DIR__, 2) . '/resources/views/layout-builder/components/widget/page/content.blade.php'))
        ->not->toContain('Frontend::page()');
});

test('breadcrumbs render data does not lazy-load optional page and site relations', function (): void {
    $language = Language::factory()->create();
    $site = Site::factory()->create(['language_id' => $language->getKey()]);
    $page = Page::factory()->site($site)->create();
    $widget = new Element(['key' => 'breadcrumbs', 'name' => 'Breadcrumbs', 'meta' => ['view_file' => 'capell-layout-builder::components.widget.page.breadcrumbs']]);

    app()->instance(CapellFrontendContext::class, new CapellFrontendContext(new FrontendContext(
        site: $site,
        language: $language,
        page: $page,
        layout: null,
        theme: null,
        params: [],
        slug: null,
    )));
    Frontend::clearResolvedInstance(CapellFrontendContext::class);

    $previous = EloquentModel::preventsLazyLoading();
    EloquentModel::preventLazyLoading();

    try {
        $component = new BreadcrumbsWidget(
            container: [],
            containerKey: 'main',
            widgetIndex: 0,
            loop: (object) ['index' => 0],
            widget: $widget,
        );

        expect($component->render())->toBeInstanceOf(View::class);
    } finally {
        EloquentModel::preventLazyLoading($previous);
    }
});

test('content page widget ignores contextless hydration when resolving next previous links', function (): void {
    app()->instance(CapellFrontendContext::class, new CapellFrontendContext(new FrontendContext(
        site: null,
        language: null,
        page: null,
        layout: null,
        theme: null,
        params: [],
        slug: null,
    )));
    Frontend::clearResolvedInstance(CapellFrontendContext::class);

    $widget = new Element(['key' => 'content', 'name' => 'Content', 'meta' => ['view_file' => 'capell::components.no-results']]);

    $component = new ContentWidget(
        container: [],
        containerKey: 'main',
        widgetIndex: 0,
        loop: (object) ['index' => 0],
        widget: $widget,
    );

    expect($component->previousPage)->toBeNull()
        ->and($component->nextPage)->toBeNull();
});

test('public livewire widgets reject references from another frontend site', function (): void {
    $language = Language::factory()->create();
    $currentSite = Site::factory()->create(['language_id' => $language->getKey()]);
    $otherSite = Site::factory()->create(['language_id' => $language->getKey()]);
    $element = Element::factory()->create(['key' => 'featured-pages']);
    $layout = Layout::factory()->site($otherSite)->create([
        'containers' => [
            'main' => [
                'elements' => [
                    ['element_key' => $element->key, 'occurrence' => 1],
                ],
            ],
        ],
    ]);
    $page = Page::factory()->site($currentSite)->withTranslations($language)->create();

    app()->instance(CapellFrontendContext::class, new CapellFrontendContext(new FrontendContext(
        site: $currentSite,
        language: $language,
        page: $page,
        layout: null,
        theme: null,
        params: [],
        slug: null,
    )));

    $component = new class extends LivewireWidget
    {
        protected function mountWidget(): void
        {
            $this->widget;
        }
    };

    expect(fn (): null => $component->mount(Crypt::encryptString(json_encode([
        'container_key' => 'main',
        'element_key' => $element->key,
        'language_id' => $language->getKey(),
        'layout_id' => $layout->getKey(),
        'occurrence' => 1,
        'page_id' => $page->getKey(),
        'page_type' => $page->getMorphClass(),
        'site_id' => $currentSite->getKey(),
        'widget_index' => 0,
    ], JSON_THROW_ON_ERROR))))
        ->toThrow(Exception::class, 'Element not found');
});
