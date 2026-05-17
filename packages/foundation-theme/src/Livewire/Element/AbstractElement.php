<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\Livewire\Element;

use Capell\Core\Contracts\Pageable;
use Capell\Core\Enums\AssetComponentEnum;
use Capell\Core\Models\Language;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\Theme;
use Capell\FoundationTheme\Providers\FoundationThemeServiceProvider;
use Capell\FoundationTheme\Support\View\FoundationThemeViewName;
use Capell\Frontend\Actions\Performance\RecordExtensionRenderContributionAction;
use Capell\Frontend\Facades\Frontend;
use Capell\LayoutBuilder\Enums\CapellLayoutCacheKeyEnum;
use Capell\LayoutBuilder\Models\Element;
use Capell\LayoutBuilder\Support\LayoutElementData;
use Capell\LayoutBuilder\Support\Loader\LayoutLoader;
use Closure;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Throwable;

/**
 * @property-read Element $element
 */
abstract class AbstractElement extends Component
{
    public string $elementReference = '';

    protected string $containerKey = '';

    protected int $elementIndex = 0;

    protected int $occurrence = 1;

    protected string $elementKey = '';

    protected ?int $layoutId = null;

    protected ?int $languageId = null;

    protected ?int $pageId = null;

    protected ?string $pageType = null;

    protected ?int $siteId = null;

    /** @var array<string, mixed> */
    protected array $referenceElementData = [];

    protected bool $resolvedLayoutLoaded = false;

    protected ?Layout $resolvedLayout = null;

    protected bool $resolvedLanguageLoaded = false;

    protected ?Language $resolvedLanguage = null;

    protected bool $resolvedPageLoaded = false;

    protected ?Pageable $resolvedPage = null;

    protected bool $resolvedSiteLoaded = false;

    protected ?Site $resolvedSite = null;

    protected bool $resolvedThemeLoaded = false;

    protected ?Theme $resolvedTheme = null;

    protected static string $defaultView = 'capell-foundation-theme::components.element.default';

    protected bool $skipRender = false;

    abstract protected function mountElement(): void;

    public static function getViewName(): string
    {
        return static::$defaultView;
    }

    public static function getElementByKey(string $elementKey): ?Element
    {
        $cacheKey = CapellLayoutCacheKeyEnum::ElementByKey->value . $elementKey;

        return self::getCached(
            $cacheKey,
            fn () => Element::query()->firstWhere('key', $elementKey),
        );
    }

    public function hydrate(): void
    {
        $this->initializeFromElementReference();
        $this->initializeElement();
    }

    /**
     * @param  array<string, mixed>  $elementData
     */
    public function mount(string $elementReference, array $elementData = []): void
    {
        $this->elementReference = $elementReference;
        $this->initializeFromElementReference();

        $this->initializeElement();
    }

    #[Computed]
    public function element(): Element
    {
        $element = $this->resolveScopedElement();

        throw_if(! $element instanceof Element, Exception::class, 'Element not found');

        return $element;
    }

    public function render(array $data = []): View|Closure|string
    {
        if ($this->skipRender) {
            return '<div style="display: none"></div>';
        }

        $this->recordNonCacheableRenderContribution();

        $data = array_merge([
            'container' => $this->containerData(),
            'containerKey' => $this->containerKey,
            'containerWidth' => null,
            'component_item' => $this->getComponentItem(),
            'hasPrimaryHeading' => (bool) $this->frontendData('has_primary_heading'),
            'index' => $this->elementIndex,
            'language' => $this->currentLanguage(),
            'layout' => $this->currentLayout(),
            'loop' => (object) ['index' => $this->elementIndex],
            'pageRecord' => $this->currentPage(),
            'urlParams' => $this->frontendParams(),
            'site' => $this->currentSite(),
            'theme' => $this->currentTheme(),
            'element' => $this->element,
            'elementData' => $this->elementData(),
        ], $data);

        return view($this->getComponent(), $data);
    }

    /**
     * Retrieve (and store if missing) a cached value using the array cache driver.
     */
    protected static function getCached(string $key, callable $resolver, bool $asBool = false): mixed
    {
        $cached = Cache::driver('array')->get($key);
        if ($cached !== null) {
            return $asBool ? (bool) $cached : $cached;
        }

        $result = $resolver();
        Cache::driver('array')->forever($key, $result);

        return $asBool ? (bool) $result : $result;
    }

    protected function getComponent(): string
    {
        return FoundationThemeViewName::canonical(
            $this->element->meta['view_file'] ?? $this->element->type->meta['view_file'] ?? static::$defaultView,
        );
    }

    protected function getComponentItem(): string
    {
        return $this->element->meta['component_item'] ?? $this->element->type->meta['component_item'] ?? $this->getDefaultComponentItem();
    }

    protected function getDefaultComponentItem(): string
    {
        return AssetComponentEnum::Card->value;
    }

    protected function initializeElement(): void
    {
        $this->mountElement();

        if ($this->skipRender) {
            $this->skipRender('<div style="display: none"></div>');
        }
    }

    private function initializeFromElementReference(): void
    {
        try {
            $reference = json_decode(Crypt::decryptString($this->elementReference), true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            $reference = [];
        }

        $containerKey = $reference['container_key'] ?? null;
        $elementKey = $reference['element_key'] ?? null;
        $languageId = $reference['language_id'] ?? null;
        $layoutId = $reference['layout_id'] ?? null;
        $occurrence = $reference['occurrence'] ?? null;
        $pageId = $reference['page_id'] ?? null;
        $pageType = $reference['page_type'] ?? null;
        $siteId = $reference['site_id'] ?? null;
        $elementData = $reference['element_data'] ?? [];
        $elementIndex = $reference['element_index'] ?? null;

        throw_if(! is_string($containerKey) || $containerKey === '' || ! is_string($elementKey) || $elementKey === '', Exception::class, 'Element reference is invalid');

        $this->containerKey = $containerKey;
        $this->elementKey = $elementKey;
        $this->languageId = is_numeric($languageId) ? (int) $languageId : null;
        $this->layoutId = is_numeric($layoutId) ? (int) $layoutId : null;
        $this->occurrence = is_numeric($occurrence) ? max(1, (int) $occurrence) : 1;
        $this->pageId = is_numeric($pageId) ? (int) $pageId : null;
        $this->pageType = is_string($pageType) && $pageType !== '' ? $pageType : null;
        $this->siteId = is_numeric($siteId) ? (int) $siteId : null;
        $this->referenceElementData = is_array($elementData) ? $elementData : [];
        $this->elementIndex = is_numeric($elementIndex) ? max(0, (int) $elementIndex) : 0;

        throw_if($this->pageId === null || $this->siteId === null, Exception::class, 'Element reference is invalid');

        $this->clearResolvedContext();
    }

    private function resolveScopedElement(): ?Element
    {
        $layout = $this->currentLayout();
        $language = $this->currentLanguage();

        if (! $layout instanceof Layout || ! $language instanceof Language) {
            return null;
        }

        if (! $this->layoutBelongsToCurrentContext($layout)) {
            return null;
        }

        return resolve(LayoutLoader::class)->getLayoutElement(
            layout: $layout,
            elementKey: $this->elementKey,
            language: $language,
            page: $this->currentPage(),
            containerKey: $this->containerKey,
            occurrence: $this->occurrence,
            containerKeys: [$this->containerKey],
        );
    }

    private function currentLayout(): ?Layout
    {
        if ($this->resolvedLayoutLoaded) {
            return $this->resolvedLayout;
        }

        try {
            $layout = Frontend::layout();
        } catch (Throwable) {
            $layout = null;
        }

        if ($layout instanceof Layout) {
            $this->resolvedLayout = $layout;
            $this->resolvedLayoutLoaded = true;

            return $this->resolvedLayout;
        }

        $this->resolvedLayout = $this->layoutId === null ? null : Layout::query()->find($this->layoutId);
        $this->resolvedLayoutLoaded = true;

        return $this->resolvedLayout;
    }

    private function currentLanguage(): ?Language
    {
        if ($this->resolvedLanguageLoaded) {
            return $this->resolvedLanguage;
        }

        try {
            $language = Frontend::language();
        } catch (Throwable) {
            $language = null;
        }

        if ($language instanceof Language) {
            $this->resolvedLanguage = $language;
            $this->resolvedLanguageLoaded = true;

            return $this->resolvedLanguage;
        }

        $this->resolvedLanguage = $this->languageId === null ? null : Language::query()->find($this->languageId);
        $this->resolvedLanguageLoaded = true;

        return $this->resolvedLanguage;
    }

    private function currentPage(): ?Pageable
    {
        if ($this->resolvedPageLoaded) {
            return $this->resolvedPage;
        }

        try {
            $page = Frontend::page();
        } catch (Throwable) {
            $page = null;
        }

        if ($page instanceof Pageable) {
            $this->resolvedPage = $page;
            $this->resolvedPageLoaded = true;

            return $this->resolvedPage;
        }

        if ($this->pageId === null) {
            $this->resolvedPageLoaded = true;

            return null;
        }

        $pageClass = $this->pageType !== null ? Relation::getMorphedModel($this->pageType) : null;
        $pageClass ??= Page::class;

        if (! is_a($pageClass, Pageable::class, true)) {
            $this->resolvedPageLoaded = true;

            return null;
        }

        try {
            $page = $pageClass::query()->with(['translation', 'type', 'image'])->find($this->pageId);
        } catch (Throwable) {
            $page = $pageClass::query()->find($this->pageId);
        }

        $this->resolvedPage = $page instanceof Pageable ? $page : null;
        $this->resolvedPageLoaded = true;

        return $this->resolvedPage;
    }

    /**
     * @return array<string, mixed>
     */
    private function containerData(): array
    {
        $layout = $this->currentLayout();
        $container = $layout instanceof Layout ? ($layout->containers[$this->containerKey] ?? []) : [];

        return is_array($container) ? $container : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function elementData(): array
    {
        $elementData = [
            'element_key' => $this->elementKey,
            'occurrence' => $this->occurrence,
        ];

        foreach (LayoutElementData::normalizeMany($this->containerData()['elements'] ?? []) as $layoutElementData) {
            if (
                LayoutElementData::key($layoutElementData) === $this->elementKey
                && LayoutElementData::occurrence($layoutElementData) === $this->occurrence
            ) {
                return array_merge($layoutElementData, $this->referenceElementData, $elementData);
            }
        }

        return array_merge($this->referenceElementData, $elementData);
    }

    private function layoutBelongsToCurrentContext(Layout $layout): bool
    {
        $site = $this->currentSite();
        $page = $this->currentPage();
        $pageSiteId = $page?->site_id ?? null;

        if ($site instanceof Site && $this->siteId !== null && (int) $site->getKey() !== $this->siteId) {
            return false;
        }

        if (is_numeric($pageSiteId) && $this->siteId !== null && (int) $pageSiteId !== $this->siteId) {
            return false;
        }

        if ($site instanceof Site && is_numeric($pageSiteId) && (int) $pageSiteId !== (int) $site->getKey()) {
            return false;
        }

        if ($layout->site_id === null) {
            return true;
        }

        if ($site instanceof Site && $layout->site_id !== (int) $site->getKey()) {
            return false;
        }

        if ($this->siteId !== null && $layout->site_id !== $this->siteId) {
            return false;
        }

        if (is_numeric($pageSiteId) && (int) $pageSiteId !== $layout->site_id) {
            return false;
        }

        return true;
    }

    private function currentSite(): ?Site
    {
        if ($this->resolvedSiteLoaded) {
            return $this->resolvedSite;
        }

        try {
            $site = Frontend::site();
        } catch (Throwable) {
            $site = null;
        }

        if ($site instanceof Site) {
            $this->resolvedSite = $site;
            $this->resolvedSiteLoaded = true;

            return $this->resolvedSite;
        }

        $this->resolvedSite = $this->siteId === null ? null : Site::query()->find($this->siteId);
        $this->resolvedSiteLoaded = true;

        return $this->resolvedSite;
    }

    private function currentTheme(): ?Theme
    {
        if ($this->resolvedThemeLoaded) {
            return $this->resolvedTheme;
        }

        try {
            $theme = Frontend::theme();
        } catch (Throwable) {
            $theme = null;
        }

        $this->resolvedTheme = $theme instanceof Theme ? $theme : null;
        $this->resolvedThemeLoaded = true;

        return $this->resolvedTheme;
    }

    /**
     * @return array<string, mixed>
     */
    private function frontendParams(): array
    {
        try {
            return Frontend::params();
        } catch (Throwable) {
            return [];
        }
    }

    private function frontendData(string $key): mixed
    {
        try {
            return Frontend::getFrontendData($key);
        } catch (Throwable) {
            return null;
        }
    }

    private function clearResolvedContext(): void
    {
        $this->resolvedLayoutLoaded = false;
        $this->resolvedLayout = null;
        $this->resolvedLanguageLoaded = false;
        $this->resolvedLanguage = null;
        $this->resolvedPageLoaded = false;
        $this->resolvedPage = null;
        $this->resolvedSiteLoaded = false;
        $this->resolvedSite = null;
        $this->resolvedThemeLoaded = false;
        $this->resolvedTheme = null;
    }

    private function recordNonCacheableRenderContribution(): void
    {
        RecordExtensionRenderContributionAction::run(
            packageName: FoundationThemeServiceProvider::$packageName,
            surface: 'frontend',
            contributionType: 'livewire-component',
            contributionClass: static::class,
            elapsedMilliseconds: 0.0,
            frontendRenderBudgetMs: 20,
            cacheTags: ['foundation-theme'],
            cacheable: false,
            sensitiveOutput: false,
            variesBy: ['site', 'locale'],
        );
    }
}
