# Site Discovery Package Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extract sitemap and public discoverability logic from SEO Suite into a new required `capell-app/site-discovery` package without making SEO Suite depend on sitemap internals.

**Architecture:** `capell-app/site-discovery` owns neutral discoverable-page/URL APIs and sitemap outputs. SEO Suite requires Site Discovery and uses only discovery-facing actions/data/contracts for AI Discovery. Sitemap-specific classes remain inside `Capell\SiteDiscovery\Support\Sitemap` and must not be imported by SEO Suite.

**Tech Stack:** PHP 8.2, Laravel/Testbench, Filament, Livewire, Spatie package tools, Pest, Spatie Laravel Data, `icamys/php-sitemap-generator`.

---

## File Structure

Create:

- `packages/site-discovery/composer.json` - standalone package metadata and provider auto-discovery.
- `packages/site-discovery/capell.json` - Capell package manifest.
- `packages/site-discovery/README.md` - product/package docs.
- `packages/site-discovery/resources/lang/en/*.php` - sitemap/discovery strings moved from SEO Suite when sitemap-specific.
- `packages/site-discovery/resources/views/**` - sitemap page/tool views moved from SEO Suite.
- `packages/site-discovery/src/Providers/SiteDiscoveryServiceProvider.php` - package registration, commands, views, Livewire, admin extenders, listeners, discovery binding.
- `packages/site-discovery/src/Actions/DiscoverPublicPagesAction.php` - neutral page discovery query replacing `PagesForSitemap`.
- `packages/site-discovery/src/Actions/DiscoverPublicUrlsAction.php` - converts discoverable pages and contributed URLs into URL data.
- `packages/site-discovery/src/Contracts/DiscoverableUrlSource.php` - extension contract for package-contributed URLs.
- `packages/site-discovery/src/Data/DiscoverablePageData.php` - page discovery DTO for public consumers.
- `packages/site-discovery/src/Data/DiscoverableUrlData.php` - URL discovery DTO for sitemap output and consumers.
- `packages/site-discovery/src/Support/Sitemap/**` - moved sitemap internals.
- `packages/site-discovery/tests/**` - moved sitemap tests plus new discovery boundary tests.

Modify:

- `composer.json` - add `Capell\SiteDiscovery` PSR-4 and test namespaces.
- `composer.local.json` - add `Capell\SiteDiscovery` PSR-4 and test namespaces.
- `packages/seo-suite/composer.json` - add `capell-app/site-discovery`, remove `icamys/php-sitemap-generator`.
- `packages/seo-suite/capell.json` - add required dependency and remove direct sitemap wording.
- `packages/seo-suite/src/Providers/SeoSuiteServiceProvider.php` - remove sitemap registrations, imports, commands, pages, listeners, Livewire components.
- `packages/seo-suite/src/Actions/BuildAiDiscoveryPageEntriesAction.php` - use Site Discovery public page discovery.
- `packages/seo-suite/src/Actions/SyncAiDiscoveryPageProfilesAction.php` - use Site Discovery public page discovery.
- `packages/seo-suite/tests/SeoSuiteTestCase.php` - register Site Discovery provider and remove SEO Suite sitemap page contribution.
- `packages/seo-suite/tests/Arch/SeoSuiteBoundaryTest.php` - allow only Site Discovery public namespace usage, reject sitemap internals.
- `packages/seo-suite/README.md`, `packages/seo-suite/docs/**`, `docs/README.md`, `docs/openai-integration.md`, `packages/diagnostics/src/Actions/Dashboard/BuildPackagesInstalledAction.php` - update docs/metadata.

Delete from SEO Suite after moving:

- `packages/seo-suite/src/Actions/GenerateSitemapAction.php`
- `packages/seo-suite/src/Console/Commands/XmlSitemapCommand.php`
- `packages/seo-suite/src/Contracts/Sitemapable.php`
- `packages/seo-suite/src/Data/SiteMapData.php`
- `packages/seo-suite/src/Data/SitemapPageData.php`
- `packages/seo-suite/src/Data/SitemapUrlItemData.php`
- `packages/seo-suite/src/Enums/SitemapCacheKey.php`
- `packages/seo-suite/src/Exceptions/SitemapGeneratorException.php`
- `packages/seo-suite/src/Filament/Extenders/Page/SitemapResourceHeaderActionExtender.php`
- `packages/seo-suite/src/Filament/Extenders/Site/SitemapSiteHeaderActionExtender.php`
- `packages/seo-suite/src/Filament/Extenders/Site/SitemapSiteRecordActionExtender.php`
- `packages/seo-suite/src/Filament/Pages/SitemapPage.php`
- `packages/seo-suite/src/Listeners/Sitemap/**`
- `packages/seo-suite/src/Livewire/Page/Sitemap.php`
- `packages/seo-suite/src/Livewire/Tools/SitemapTool.php`
- `packages/seo-suite/src/Support/AdminTools/SitemapAdminTool.php`
- `packages/seo-suite/src/Support/Creator/SitemapPageCreator.php`
- `packages/seo-suite/src/Support/Interceptors/SitemapPageTypeInterceptor.php`
- `packages/seo-suite/src/Support/Loader/SitemapLoader.php`
- `packages/seo-suite/src/Support/Sitemap/**`
- sitemap-specific tests and views listed in the tasks below.

---

### Task 1: Scaffold Site Discovery Package

**Files:**

- Create: `packages/site-discovery/composer.json`
- Create: `packages/site-discovery/capell.json`
- Create: `packages/site-discovery/README.md`
- Create: `packages/site-discovery/src/Providers/SiteDiscoveryServiceProvider.php`
- Create: `packages/site-discovery/tests/Pest.php`
- Create: `packages/site-discovery/tests/SiteDiscoveryTestCase.php`
- Create: `packages/site-discovery/tests/Arch/SiteDiscoveryBoundaryTest.php`
- Modify: `composer.json`
- Modify: `composer.local.json`

- [ ] **Step 1: Create package metadata**

Create `packages/site-discovery/composer.json`:

```json
{
    "name": "capell-app/site-discovery",
    "description": "Public discoverability and sitemap outputs for Capell",
    "keywords": [
        "capell",
        "discovery",
        "sitemap",
        "seo",
        "laravel",
        "filamentphp",
        "cms"
    ],
    "license": "proprietary",
    "require": {
        "php": "^8.2",
        "capell-app/admin": "*",
        "capell-app/core": "*",
        "capell-app/frontend": "*",
        "icamys/php-sitemap-generator": "^4.4"
    },
    "require-dev": {
        "orchestra/testbench": "^9.0"
    },
    "autoload": {
        "psr-4": {
            "Capell\\SiteDiscovery\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Capell\\SiteDiscovery\\Providers\\SiteDiscoveryServiceProvider"
            ]
        }
    },
    "config": {
        "sort-packages": true
    },
    "prefer-stable": true
}
```

Create `packages/site-discovery/capell.json`:

```json
{
    "manifest-version": 2,
    "name": "capell-app/site-discovery",
    "kind": "package",
    "capell-version": "^4.0",
    "productGroup": "Capell Search & SEO",
    "tier": "premium",
    "bundle": "search-seo",
    "description": "Site Discovery resolves public discoverable pages and URLs, then exposes HTML and XML sitemap outputs.",
    "surfaces": ["admin", "frontend", "console"],
    "dependencies": {
        "requires": [
            "capell-app/core",
            "capell-app/admin",
            "capell-app/frontend"
        ],
        "optional": [],
        "conflicts": []
    },
    "lifecycle": {
        "activation": "manual",
        "defaultStatus": "available",
        "requiresInstallCommand": false
    },
    "providers": {
        "metadata": [],
        "install": [],
        "runtime": [
            "Capell\\SiteDiscovery\\Providers\\SiteDiscoveryServiceProvider"
        ],
        "admin": [],
        "frontend": []
    },
    "database": {
        "migrations": false,
        "settings": false,
        "requiredTables": []
    },
    "commands": {
        "install": null,
        "setup": null,
        "setupParams": [],
        "demo": null,
        "demoParams": [],
        "health": null
    },
    "settings": [],
    "permissions": [],
    "capabilities": [],
    "assets": [],
    "healthChecks": []
}
```

- [ ] **Step 2: Add initial README**

Create `packages/site-discovery/README.md`:

```markdown
# Site Discovery

Status: **Available, discovery-owning** · Kind: **package** · Tier: **premium** · Bundle: **search-seo** · Contexts: **admin, frontend, console** · Product group: **Capell Search & SEO**

Site Discovery resolves public, indexable, discoverable Capell pages and exposes HTML and XML sitemap outputs.

## What This Plugin Adds

- Public discoverable page and URL APIs.
- HTML sitemap page type and frontend component.
- XML sitemap generation with chunking and incremental state.
- Sitemap admin page, admin actions, and generation tool.
- Lifecycle listeners that regenerate sitemap output when pages or sites change.

## Built With

- [PHP Sitemap Generator](https://github.com/icamys/php-sitemap-generator) - XML sitemap generation.

## Quick Start

1. Install the package with `composer require capell-app/site-discovery`.
2. Run package discovery/installation in the host app.
3. Generate sitemap output with `capell:xml-sitemap`.
```

- [ ] **Step 3: Add provider skeleton**

Create `packages/site-discovery/src/Providers/SiteDiscoveryServiceProvider.php`:

```php
<?php

declare(strict_types=1);

namespace Capell\SiteDiscovery\Providers;

use Capell\Core\Data\PackageData;
use Capell\Core\Enums\PackageTypeEnum;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Composer\InstalledVersions;
use Spatie\LaravelPackageTools\Package;

final class SiteDiscoveryServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-site-discovery';

    public static string $packageName = 'capell-app/site-discovery';

    public static PackageTypeEnum $type = PackageTypeEnum::Plugin;

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasViews(self::$name)
            ->hasTranslations();
    }

    public function registeringPackage(): void
    {
        $this->registerPackageMetadata();

        $this->app->booted(function (): void {
            if (! $this->isPackageInstalled()) {
                return;
            }

            $this->bootInstalledPackage();
        });
    }

    private function bootInstalledPackage(): self
    {
        return $this;
    }

    private function isPackageInstalled(): bool
    {
        $package = CapellCore::getPackage(static::$packageName);

        return $package instanceof PackageData && $package->isInstalled();
    }

    private function registerPackageMetadata(): void
    {
        CapellCore::registerPackage(
            static::$packageName,
            type: static::getType(),
            serviceProviderClass: static::class,
            path: realpath(__DIR__ . '/../..'),
            version: $this->getVersion(),
            permissions: [],
            description: fn (): string => __('capell-site-discovery::package.description'),
        );
    }

    private function getVersion(): string
    {
        if (! class_exists(InstalledVersions::class)) {
            return 'dev';
        }

        if (! InstalledVersions::isInstalled(static::$packageName)) {
            return 'dev';
        }

        return InstalledVersions::getPrettyVersion(static::$packageName) ?? 'dev';
    }
}
```

- [ ] **Step 4: Add initial test case**

Create `packages/site-discovery/tests/SiteDiscoveryTestCase.php`:

```php
<?php

declare(strict_types=1);

namespace Capell\SiteDiscovery\Tests;

use Capell\Admin\Providers\AdminServiceProvider;
use Capell\Admin\Providers\Filament\AdminPanelProvider;
use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Contracts\FrontendContextReader;
use Capell\Frontend\Providers\FrontendServiceProvider;
use Capell\Frontend\Support\CapellFrontendContext;
use Capell\Frontend\Support\State\FrontendState;
use Capell\Navigation\Providers\NavigationServiceProvider;
use Capell\SiteDiscovery\Providers\SiteDiscoveryServiceProvider;
use Capell\Tests\AbstractTestCase;
use Illuminate\Contracts\Foundation\Application;
use Livewire\LivewireServiceProvider;
use MichalOravec\PaginateRoute\PaginateRouteServiceProvider;
use Override;

class SiteDiscoveryTestCase extends AbstractTestCase
{
    protected function getPackageServiceName(): string
    {
        return 'capell-site-discovery';
    }

    /**
     * @return class-string[]
     */
    #[Override]
    protected function getPackageProviders(mixed $app): array
    {
        return [
            ...parent::getPackageProviders($app),
            AdminServiceProvider::class,
            SiteDiscoveryServiceProvider::class,
            AdminPanelProvider::class,
            FrontendServiceProvider::class,
            LivewireServiceProvider::class,
            NavigationServiceProvider::class,
            PaginateRouteServiceProvider::class,
        ];
    }

    #[Override]
    protected function getEnvironmentSetUp(mixed $app): void
    {
        parent::getEnvironmentSetUp($app);

        $app->scoped(FrontendState::class, fn (): FrontendState => new FrontendState);
        $app->scoped(FrontendContextReader::class, fn (Application $application): FrontendState => $application->make(FrontendState::class));
        $app->scoped(CapellFrontendContext::class, fn (Application $application): CapellFrontendContext => new CapellFrontendContext($application->make(FrontendContextReader::class)));
        $app->alias(CapellFrontendContext::class, 'capell.frontend.context');

        CapellCore::forcePackageInstalled(AdminServiceProvider::$packageName);
        CapellCore::registerPackage(
            FrontendServiceProvider::$packageName,
            path: realpath(__DIR__ . '/../../frontend'),
        );
        CapellCore::forcePackageInstalled(FrontendServiceProvider::$packageName);
        CapellCore::forcePackageInstalled(SiteDiscoveryServiceProvider::$packageName);

        CapellCore::registerPackage(
            NavigationServiceProvider::$packageName,
            path: realpath(__DIR__ . '/../../navigation'),
        );
        CapellCore::forcePackageInstalled(NavigationServiceProvider::$packageName);
    }
}
```

Create `packages/site-discovery/tests/Pest.php`:

```php
<?php

declare(strict_types=1);

use Capell\SiteDiscovery\Tests\SiteDiscoveryTestCase;

pest()->extend(SiteDiscoveryTestCase::class)->group('site-discovery')->in(__DIR__);
```

- [ ] **Step 5: Add initial boundary test**

Create `packages/site-discovery/tests/Arch/SiteDiscoveryBoundaryTest.php`:

```php
<?php

declare(strict_types=1);

arch('site-discovery does not import consumer packages')
    ->expect('Capell\SiteDiscovery')
    ->not->toUse([
        'Capell\Blog',
        'Capell\SeoSuite',
        'Capell\Search',
        'Capell\Tags',
        'Capell\PublishingStudio',
    ]);

arch()
    ->expect('Capell\SiteDiscovery')
    ->classes()
    ->toUseStrictEquality();
```

- [ ] **Step 6: Register autoload namespaces**

In root `composer.json` and `composer.local.json`, add to `autoload.psr-4` near `Capell\\SeoSuite\\`:

```json
"Capell\\SiteDiscovery\\": "packages/site-discovery/src",
```

Add to `autoload-dev.psr-4` near `Capell\\SeoSuite\\Tests\\`:

```json
"Capell\\SiteDiscovery\\Tests\\": "packages/site-discovery/tests",
```

- [ ] **Step 7: Regenerate autoload and run scaffold tests**

Run:

```bash
COMPOSER=composer.local.json composer dump-autoload --no-scripts
vendor/bin/pest packages/site-discovery/tests/Arch --configuration=phpunit.xml
```

Expected: autoload generation succeeds; Site Discovery arch test passes.

- [ ] **Step 8: Commit scaffold**

```bash
git add composer.json composer.local.json packages/site-discovery
git commit -m "feat(site-discovery): scaffold package"
```

---

### Task 2: Add Neutral Discovery API

**Files:**

- Create: `packages/site-discovery/src/Data/DiscoverablePageData.php`
- Create: `packages/site-discovery/src/Data/DiscoverableUrlData.php`
- Create: `packages/site-discovery/src/Contracts/DiscoverableUrlSource.php`
- Create: `packages/site-discovery/src/Actions/DiscoverPublicPagesAction.php`
- Create: `packages/site-discovery/src/Actions/DiscoverPublicUrlsAction.php`
- Create: `packages/site-discovery/tests/Integration/Discovery/DiscoverPublicPagesActionTest.php`
- Create: `packages/site-discovery/tests/Unit/Data/DiscoverableUrlDataTest.php`

- [ ] **Step 1: Add discovery data objects**

Create `packages/site-discovery/src/Data/DiscoverablePageData.php`:

```php
<?php

declare(strict_types=1);

namespace Capell\SiteDiscovery\Data;

use Capell\Core\Models\Page;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;

final class DiscoverablePageData extends Data
{
    public function __construct(
        public readonly int $pageId,
        public readonly string $title,
        public readonly string $url,
        public readonly ?CarbonInterface $lastModified = null,
        public readonly ?float $priority = null,
        public readonly ?string $changeFrequency = null,
        public readonly ?Page $page = null,
    ) {}
}
```

Create `packages/site-discovery/src/Data/DiscoverableUrlData.php`:

```php
<?php

declare(strict_types=1);

namespace Capell\SiteDiscovery\Data;

use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;

final class DiscoverableUrlData extends Data
{
    public function __construct(
        public readonly string $loc,
        public readonly ?CarbonInterface $lastModified = null,
        public readonly ?string $changeFrequency = null,
        public readonly ?string $priority = null,
    ) {}
}
```

- [ ] **Step 2: Add contributor contract**

Create `packages/site-discovery/src/Contracts/DiscoverableUrlSource.php`:

```php
<?php

declare(strict_types=1);

namespace Capell\SiteDiscovery\Contracts;

use Capell\Core\Models\Language;
use Capell\Core\Models\Site;
use Capell\SiteDiscovery\Data\DiscoverableUrlData;
use Illuminate\Support\Collection;

interface DiscoverableUrlSource
{
    /**
     * @return Collection<int, DiscoverableUrlData>
     */
    public function discover(Site $site, Language $language): Collection;
}
```

- [ ] **Step 3: Add failing public-page discovery test**

Create `packages/site-discovery/tests/Integration/Discovery/DiscoverPublicPagesActionTest.php` by adapting `packages/seo-suite/tests/Integration/Sitemap/NoIndexPageDiscoveryTest.php` to Site Discovery:

```php
<?php

declare(strict_types=1);

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\SiteDiscovery\Actions\DiscoverPublicPagesAction;
use Capell\SiteDiscovery\Data\DiscoverablePageData;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
});

it('excludes pages that are not public discoverable pages', function (): void {
    $language = Language::query()->create([
        'name' => 'English',
        'locale' => 'en',
        'code' => 'en',
        'flag' => 'gb-eng',
        'status' => true,
        'default' => true,
        'order' => 1,
    ]);
    $site = Site::factory()->language($language)->withTranslations($language)->create();

    $publicPage = Page::factory()
        ->site($site)
        ->withTranslations($language, ['title' => 'Public Page'])
        ->create();

    Page::factory()
        ->site($site)
        ->withTranslations($language, ['title' => 'Noindex Page'])
        ->meta('robots', ['noindex'])
        ->create();

    Page::factory()
        ->site($site)
        ->withTranslations($language, ['title' => 'Hidden Page'])
        ->meta('hidden', true)
        ->create();

    $pages = DiscoverPublicPagesAction::run($site, $language);

    expect($pages)->toHaveCount(1)
        ->and($pages->first())->toBeInstanceOf(DiscoverablePageData::class)
        ->and($pages->pluck('pageId')->all())->toBe([(int) $publicPage->getKey()])
        ->and($pages->first()?->title)->toBe('Public Page');
});
```

Run:

```bash
vendor/bin/pest packages/site-discovery/tests/Integration/Discovery/DiscoverPublicPagesActionTest.php --configuration=phpunit.xml
```

Expected: fail because `DiscoverPublicPagesAction` does not exist.

- [ ] **Step 4: Implement public-page discovery**

Create `packages/site-discovery/src/Actions/DiscoverPublicPagesAction.php`:

```php
<?php

declare(strict_types=1);

namespace Capell\SiteDiscovery\Actions;

use Capell\Core\Enums\TypeGroupEnum;
use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\SiteDiscovery\Data\DiscoverablePageData;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static Collection<int, DiscoverablePageData> run(Site $site, Language $language)
 */
final class DiscoverPublicPagesAction
{
    use AsAction;

    /**
     * @return Collection<int, DiscoverablePageData>
     */
    public function handle(Site $site, Language $language): Collection
    {
        $query = Page::query();

        return $query->select([
            'pages.*',
            DB::raw("json_extract(pages.meta, '$.priority') AS meta_priority"),
        ])
            ->with(['translation' => fn (BuilderContract $query): BuilderContract => $query->where('language_id', $language->id)])
            ->withWhereHas(
                'pageUrl',
                fn (BuilderContract $query): BuilderContract => $query->where('language_id', $language->id),
            )
            ->withWhereHas(
                'type',
                fn (BuilderContract $query): BuilderContract => $query
                    ->where(
                        fn (Builder $query): Builder => $query->whereNull('group')
                            ->orWhereIn('group', config('capell.core.sitemap.type_groups', [TypeGroupEnum::Default->value])),
                    )
                    ->enabled()
                    ->visible()
                    ->accessible(),
            )
            ->where($query->qualifyColumn('site_id'), $site->id)
            ->where(
                fn (Builder $query): Builder => $query->whereNull('pages.meta')
                    ->orWhereJsonDoesntContain('pages.meta->hidden', true),
            )
            ->where(
                fn (Builder $query): Builder => $query->whereNull('pages.meta->robots')
                    ->orWhereJsonDoesntContain('pages.meta->robots', 'noindex'),
            )
            ->publishedDate()
            ->ordered()
            ->get()
            ->map(function (Page $page) use ($site): DiscoverablePageData {
                $page->setRelation('site', $site);
                Page::setResolvedPageUrlSiteDomain($page, $site);

                return new DiscoverablePageData(
                    pageId: (int) $page->getKey(),
                    title: trim(strip_tags((string) ($page->translation?->title ?? $page->translation?->label ?? ''))),
                    url: (string) ($page->pageUrl?->full_url ?? ''),
                    lastModified: $page->updated_at,
                    priority: is_numeric($page->meta['priority'] ?? null) ? (float) $page->meta['priority'] : null,
                    changeFrequency: is_string($page->meta['changefreq'] ?? null) ? $page->meta['changefreq'] : null,
                    page: $page,
                );
            })
            ->filter(fn (DiscoverablePageData $page): bool => $page->url !== '' && $page->title !== '')
            ->values();
    }
}
```

- [ ] **Step 5: Add URL discovery action**

Create `packages/site-discovery/src/Actions/DiscoverPublicUrlsAction.php`:

```php
<?php

declare(strict_types=1);

namespace Capell\SiteDiscovery\Actions;

use Capell\Core\Models\Language;
use Capell\Core\Models\Site;
use Capell\SiteDiscovery\Contracts\DiscoverableUrlSource;
use Capell\SiteDiscovery\Data\DiscoverablePageData;
use Capell\SiteDiscovery\Data\DiscoverableUrlData;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @method static Collection<int, DiscoverableUrlData> run(Site $site, Language $language)
 */
final class DiscoverPublicUrlsAction
{
    use AsAction;

    /**
     * @return Collection<int, DiscoverableUrlData>
     */
    public function handle(Site $site, Language $language): Collection
    {
        $pageUrls = DiscoverPublicPagesAction::run($site, $language)
            ->map(fn (DiscoverablePageData $page): DiscoverableUrlData => new DiscoverableUrlData(
                loc: $page->url,
                lastModified: $page->lastModified,
                changeFrequency: $page->changeFrequency,
                priority: $page->priority !== null ? number_format($page->priority, 1, '.', '') : null,
            ));

        $contributedUrls = collect(app()->tagged('capell-site-discovery:discoverable-url-sources'))
            ->filter(fn (mixed $source): bool => $source instanceof DiscoverableUrlSource)
            ->flatMap(fn (DiscoverableUrlSource $source): Collection => $source->discover($site, $language));

        return $pageUrls
            ->merge($contributedUrls)
            ->unique(fn (DiscoverableUrlData $url): string => $url->loc)
            ->values();
    }
}
```

- [ ] **Step 6: Add URL data test**

Create `packages/site-discovery/tests/Unit/Data/DiscoverableUrlDataTest.php`:

```php
<?php

declare(strict_types=1);

use Capell\SiteDiscovery\Data\DiscoverableUrlData;

it('stores discoverable URL attributes for sitemap and discovery consumers', function (): void {
    $data = new DiscoverableUrlData(
        loc: 'https://example.com/about',
        changeFrequency: 'weekly',
        priority: '0.7',
    );

    expect($data->loc)->toBe('https://example.com/about')
        ->and($data->changeFrequency)->toBe('weekly')
        ->and($data->priority)->toBe('0.7');
});
```

- [ ] **Step 7: Run discovery tests**

```bash
vendor/bin/pest packages/site-discovery/tests/Integration/Discovery packages/site-discovery/tests/Unit/Data --configuration=phpunit.xml
```

Expected: pass.

- [ ] **Step 8: Commit discovery API**

```bash
git add packages/site-discovery/src packages/site-discovery/tests
git commit -m "feat(site-discovery): add public discovery API"
```

---

### Task 3: Move Sitemap Output Into Site Discovery

**Files:**

- Move: sitemap source files from `packages/seo-suite/src/**` to `packages/site-discovery/src/**`
- Move: sitemap views from `packages/seo-suite/resources/views/**` to `packages/site-discovery/resources/views/**`
- Move: sitemap tests from `packages/seo-suite/tests/**` to `packages/site-discovery/tests/**`
- Modify: `packages/site-discovery/src/Providers/SiteDiscoveryServiceProvider.php`
- Modify: `packages/site-discovery/resources/lang/en/*.php`

- [ ] **Step 1: Move source files with git mv**

Run:

```bash
mkdir -p packages/site-discovery/src/Actions \
  packages/site-discovery/src/Console/Commands \
  packages/site-discovery/src/Contracts \
  packages/site-discovery/src/Data \
  packages/site-discovery/src/Enums \
  packages/site-discovery/src/Exceptions \
  packages/site-discovery/src/Filament/Extenders/Page \
  packages/site-discovery/src/Filament/Extenders/Site \
  packages/site-discovery/src/Filament/Pages \
  packages/site-discovery/src/Listeners/Sitemap \
  packages/site-discovery/src/Livewire/Page \
  packages/site-discovery/src/Livewire/Tools \
  packages/site-discovery/src/Support/AdminTools \
  packages/site-discovery/src/Support/Creator \
  packages/site-discovery/src/Support/Interceptors \
  packages/site-discovery/src/Support/Loader \
  packages/site-discovery/src/Support/Sitemap

git mv packages/seo-suite/src/Actions/GenerateSitemapAction.php packages/site-discovery/src/Actions/GenerateSitemapAction.php
git mv packages/seo-suite/src/Console/Commands/XmlSitemapCommand.php packages/site-discovery/src/Console/Commands/XmlSitemapCommand.php
git mv packages/seo-suite/src/Contracts/Sitemapable.php packages/site-discovery/src/Contracts/Sitemapable.php
git mv packages/seo-suite/src/Data/SiteMapData.php packages/site-discovery/src/Data/SiteMapData.php
git mv packages/seo-suite/src/Data/SitemapPageData.php packages/site-discovery/src/Data/SitemapPageData.php
git mv packages/seo-suite/src/Data/SitemapUrlItemData.php packages/site-discovery/src/Data/SitemapUrlItemData.php
git mv packages/seo-suite/src/Enums/SitemapCacheKey.php packages/site-discovery/src/Enums/SitemapCacheKey.php
git mv packages/seo-suite/src/Exceptions/SitemapGeneratorException.php packages/site-discovery/src/Exceptions/SitemapGeneratorException.php
git mv packages/seo-suite/src/Filament/Extenders/Page/SitemapResourceHeaderActionExtender.php packages/site-discovery/src/Filament/Extenders/Page/SitemapResourceHeaderActionExtender.php
git mv packages/seo-suite/src/Filament/Extenders/Site/SitemapSiteHeaderActionExtender.php packages/site-discovery/src/Filament/Extenders/Site/SitemapSiteHeaderActionExtender.php
git mv packages/seo-suite/src/Filament/Extenders/Site/SitemapSiteRecordActionExtender.php packages/site-discovery/src/Filament/Extenders/Site/SitemapSiteRecordActionExtender.php
git mv packages/seo-suite/src/Filament/Pages/SitemapPage.php packages/site-discovery/src/Filament/Pages/SitemapPage.php
git mv packages/seo-suite/src/Listeners/Sitemap packages/site-discovery/src/Listeners/Sitemap
git mv packages/seo-suite/src/Livewire/Page/Sitemap.php packages/site-discovery/src/Livewire/Page/Sitemap.php
git mv packages/seo-suite/src/Livewire/Tools/SitemapTool.php packages/site-discovery/src/Livewire/Tools/SitemapTool.php
git mv packages/seo-suite/src/Support/AdminTools/SitemapAdminTool.php packages/site-discovery/src/Support/AdminTools/SitemapAdminTool.php
git mv packages/seo-suite/src/Support/Creator/SitemapPageCreator.php packages/site-discovery/src/Support/Creator/SitemapPageCreator.php
git mv packages/seo-suite/src/Support/Interceptors/SitemapPageTypeInterceptor.php packages/site-discovery/src/Support/Interceptors/SitemapPageTypeInterceptor.php
git mv packages/seo-suite/src/Support/Loader/SitemapLoader.php packages/site-discovery/src/Support/Loader/SitemapLoader.php
git mv packages/seo-suite/src/Support/Sitemap packages/site-discovery/src/Support/Sitemap
```

- [ ] **Step 2: Move sitemap views**

Run:

```bash
mkdir -p packages/site-discovery/resources/views/components/pages \
  packages/site-discovery/resources/views/livewire/page \
  packages/site-discovery/resources/views/livewire/tools \
  packages/site-discovery/resources/views/sitemap

git mv packages/seo-suite/resources/views/components/pages/sitemap.blade.php packages/site-discovery/resources/views/components/pages/sitemap.blade.php
git mv packages/seo-suite/resources/views/components/pages/sitemap packages/site-discovery/resources/views/components/pages/sitemap
git mv packages/seo-suite/resources/views/livewire/page/sitemap.blade.php packages/site-discovery/resources/views/livewire/page/sitemap.blade.php
git mv packages/seo-suite/resources/views/livewire/tools/sitemap-tool.blade.php packages/site-discovery/resources/views/livewire/tools/sitemap-tool.blade.php
git mv packages/seo-suite/resources/views/sitemap/sitemap-page.blade.php packages/site-discovery/resources/views/sitemap/sitemap-page.blade.php
```

- [ ] **Step 3: Move sitemap tests**

Run:

```bash
mkdir -p packages/site-discovery/tests/Feature/Page \
  packages/site-discovery/tests/Integration/Actions \
  packages/site-discovery/tests/Integration/Commands \
  packages/site-discovery/tests/Integration/Sitemap \
  packages/site-discovery/tests/Unit/Data \
  packages/site-discovery/tests/Unit/Sitemap \
  packages/site-discovery/tests/Unit/Support

git mv packages/seo-suite/tests/Feature/Page/PageSitemapTest.php packages/site-discovery/tests/Feature/Page/PageSitemapTest.php
git mv packages/seo-suite/tests/Integration/Actions/GenerateSitemapActionTest.php packages/site-discovery/tests/Integration/Actions/GenerateSitemapActionTest.php
git mv packages/seo-suite/tests/Integration/Commands/XmlSitemapCommandTest.php packages/site-discovery/tests/Integration/Commands/XmlSitemapCommandTest.php
git mv packages/seo-suite/tests/Integration/Sitemap/SitemapBuilderTest.php packages/site-discovery/tests/Integration/Sitemap/SitemapBuilderTest.php
git mv packages/seo-suite/tests/Integration/Sitemap/SitemapGeneratorIncrementalTest.php packages/site-discovery/tests/Integration/Sitemap/SitemapGeneratorIncrementalTest.php
git mv packages/seo-suite/tests/Integration/Sitemap/SitemapGeneratorTest.php packages/site-discovery/tests/Integration/Sitemap/SitemapGeneratorTest.php
git mv packages/seo-suite/tests/Integration/Sitemap/SitemapLifecycleListenerTest.php packages/site-discovery/tests/Integration/Sitemap/SitemapLifecycleListenerTest.php
git mv packages/seo-suite/tests/Unit/Data/SitemapPageDataTest.php packages/site-discovery/tests/Unit/Data/SitemapPageDataTest.php
git mv packages/seo-suite/tests/Unit/Sitemap/SitemapChainBuilderTest.php packages/site-discovery/tests/Unit/Sitemap/SitemapChainBuilderTest.php
git mv packages/seo-suite/tests/Unit/Sitemap/SitemapStateStoreTest.php packages/site-discovery/tests/Unit/Sitemap/SitemapStateStoreTest.php
git mv packages/seo-suite/tests/Unit/Support/SitemapGeneratorTest.php packages/site-discovery/tests/Unit/Support/SitemapGeneratorTest.php
```

- [ ] **Step 4: Rewrite namespaces and view namespaces**

Run these namespace replacements:

```bash
perl -pi -e 's/Capell\\\\SeoSuite\\\\/Capell\\\\SiteDiscovery\\\\/g' $(rg -l 'Capell\\\\SeoSuite\\\\' packages/site-discovery/src packages/site-discovery/tests)
perl -pi -e 's/namespace Capell\\\\SeoSuite/namespace Capell\\\\SiteDiscovery/g' $(rg -l 'namespace Capell\\\\SeoSuite' packages/site-discovery/src packages/site-discovery/tests)
perl -pi -e 's/capell-seo-suite::livewire\\.page\\.sitemap/capell-site-discovery::livewire.page.sitemap/g' packages/site-discovery/src/Livewire/Page/Sitemap.php
perl -pi -e 's/capell-seo-suite::livewire\\.tools\\.sitemap-tool/capell-site-discovery::livewire.tools.sitemap-tool/g' packages/site-discovery/src/Livewire/Tools/SitemapTool.php
perl -pi -e 's/capell-seo-suite::sitemap\\.sitemap-page/capell-site-discovery::sitemap.sitemap-page/g' packages/site-discovery/src/Filament/Pages/SitemapPage.php
perl -pi -e 's/capell-seo-suite::/capell-site-discovery::/g' packages/site-discovery/resources/views/livewire/tools/sitemap-tool.blade.php packages/site-discovery/resources/views/sitemap/sitemap-page.blade.php
```

Then inspect for accidental replacements that should stay as SEO Suite:

```bash
rg -n "SeoSuite|seo-suite|capell-seo-suite|Capell\\\\SeoSuite" packages/site-discovery
```

Expected: no matches except historical README text if intentionally added. Production PHP should have no matches.

- [ ] **Step 5: Keep discovery neutral inside sitemap internals**

Delete `packages/site-discovery/src/Support/Sitemap/Queries/PagesForSitemap.php` after updating `SitemapBuilder` and `XmlSitemapGenerator` to call `DiscoverPublicPagesAction` or `DiscoverPublicUrlsAction`.

In `packages/site-discovery/src/Support/Sitemap/SitemapBuilder.php`, replace page-query construction with:

```php
use Capell\SiteDiscovery\Actions\DiscoverPublicPagesAction;
use Capell\SiteDiscovery\Data\DiscoverablePageData;

// Inside the builder where page models were loaded:
$pages = DiscoverPublicPagesAction::run($this->site, $this->language)
    ->map(fn (DiscoverablePageData $data): ?Page => $data->page)
    ->filter(fn (?Page $page): bool => $page instanceof Page)
    ->values();
```

In `packages/site-discovery/src/Support/Sitemap/XmlSitemapGenerator.php`, prefer `DiscoverPublicUrlsAction` for flat XML URL output:

```php
use Capell\SiteDiscovery\Actions\DiscoverPublicUrlsAction;
use Capell\SiteDiscovery\Data\DiscoverableUrlData;

// Replace flattening of sitemap page data for XML output with:
$items = DiscoverPublicUrlsAction::run($site, $domain->language)
    ->map(fn (DiscoverableUrlData $url): SitemapUrlItemData => new SitemapUrlItemData(
        loc: $url->loc,
        lastmod: $url->lastModified,
        changefreq: $url->changeFrequency,
        priority: $url->priority,
    ))
    ->all();
```

If the existing XML generator still needs hierarchical page data for HTML sitemap output, keep `SitemapBuilder` for HTML and use `DiscoverPublicUrlsAction` only for XML.

- [ ] **Step 6: Add language files**

Create `packages/site-discovery/resources/lang/en/package.php`:

```php
<?php

declare(strict_types=1);

return [
    'description' => 'Site Discovery resolves public discoverable pages and URLs, then exposes HTML and XML sitemap outputs.',
];
```

Create `packages/site-discovery/resources/lang/en/generic.php` with sitemap-specific keys copied from `packages/seo-suite/resources/lang/en/generic.php`. Keep only keys needed by moved sitemap classes/views, including:

```php
<?php

declare(strict_types=1);

return [
    'sitemap' => 'Sitemap',
];
```

Create `packages/site-discovery/resources/lang/en/message.php` with sitemap-specific notification keys copied from `packages/seo-suite/resources/lang/en/message.php`.

- [ ] **Step 7: Register moved sitemap capabilities in Site Discovery provider**

Modify `packages/site-discovery/src/Providers/SiteDiscoveryServiceProvider.php` to import and register the moved classes:

```php
use Capell\Admin\Contracts\AdminTools\AdminToolItem;
use Capell\Admin\Contracts\Extenders\ResourceHeaderActionExtender;
use Capell\Admin\Contracts\Extenders\SiteHeaderActionExtender;
use Capell\Admin\Contracts\Extenders\SiteRecordActionExtender;
use Capell\Admin\Support\CapellAdminManager;
use Capell\Core\Actions\RegisterBlazeOptimizedViewsAction;
use Capell\Core\Enums\TypeEnum;
use Capell\Core\Events\PageDeleted;
use Capell\Core\Events\PageSaved;
use Capell\Core\Events\SiteCreated;
use Capell\Core\Models\Site;
use Capell\Core\Models\Blueprint;
use Capell\Core\Facades\CapellCore;
use Capell\SiteDiscovery\Console\Commands\XmlSitemapCommand;
use Capell\SiteDiscovery\Filament\Extenders\Page\SitemapResourceHeaderActionExtender;
use Capell\SiteDiscovery\Filament\Extenders\Site\SitemapSiteHeaderActionExtender;
use Capell\SiteDiscovery\Filament\Extenders\Site\SitemapSiteRecordActionExtender;
use Capell\SiteDiscovery\Filament\Pages\SitemapPage;
use Capell\SiteDiscovery\Listeners\Sitemap\RegenerateSitemapsOnPageDeleted;
use Capell\SiteDiscovery\Listeners\Sitemap\RegenerateSitemapsOnPageSaved;
use Capell\SiteDiscovery\Listeners\Sitemap\RegenerateSitemapsOnSiteCreated;
use Capell\SiteDiscovery\Livewire\Page\Sitemap as SitemapLivewireComponent;
use Capell\SiteDiscovery\Livewire\Tools\SitemapTool;
use Capell\SiteDiscovery\Support\AdminTools\SitemapAdminTool;
use Capell\SiteDiscovery\Support\Creator\SitemapPageCreator;
use Capell\SiteDiscovery\Support\Interceptors\SitemapPageTypeInterceptor;
use Capell\SiteDiscovery\Support\Sitemap\Pages\PagesSitemap;
use Capell\SiteDiscovery\Support\Sitemap\SitemapPageRegistry;
use Capell\SiteDiscovery\Support\Sitemap\SitemapPageType;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Livewire\Livewire;
```

Update `configurePackage()`:

```php
$package
    ->name(self::$name)
    ->hasViews(self::$name)
    ->hasTranslations()
    ->hasCommands([
        XmlSitemapCommand::class,
    ]);
```

Update `bootInstalledPackage()` to call:

```php
return $this
    ->registerBlazeComponents()
    ->registerAdminExtenders()
    ->registerFilamentPages()
    ->registerLivewireComponents()
    ->registerSitemapPageType()
    ->registerSitemapDefaultPage()
    ->registerSitemapRegistry()
    ->registerSitemapEventListeners();
```

Add the helper methods by moving the existing SEO Suite methods into this provider and updating namespaces:

```php
protected function registerAdminExtenders(): self
{
    $this->app->tag([
        SitemapSiteHeaderActionExtender::class,
    ], SiteHeaderActionExtender::TAG);

    $this->app->tag([
        SitemapResourceHeaderActionExtender::class,
    ], ResourceHeaderActionExtender::TAG);

    $this->app->tag([
        SitemapSiteRecordActionExtender::class,
    ], SiteRecordActionExtender::TAG);

    $this->app->tag([
        SitemapAdminTool::class,
    ], AdminToolItem::TAG);

    return $this;
}
```

Also move/register `registerFilamentPages()`, `registerLivewireComponents()`, `registerSitemapPageType()`, `registerSitemapDefaultPage()`, `registerSitemapRegistry()`, `registerSitemapEventListeners()`, and `registerBlazeComponents()` from SEO Suite.

- [ ] **Step 8: Run moved sitemap tests**

Run:

```bash
vendor/bin/pest packages/site-discovery/tests --configuration=phpunit.xml
```

Expected: pass after namespace/import/view translation fixes.

- [ ] **Step 9: Commit sitemap move**

```bash
git add packages/site-discovery packages/seo-suite/src packages/seo-suite/resources packages/seo-suite/tests
git commit -m "refactor(site-discovery): move sitemap output from seo suite"
```

---

### Task 4: Make SEO Suite Depend On Site Discovery

**Files:**

- Modify: `packages/seo-suite/composer.json`
- Modify: `packages/seo-suite/capell.json`
- Modify: `packages/seo-suite/src/Providers/SeoSuiteServiceProvider.php`
- Modify: `packages/seo-suite/src/Actions/BuildAiDiscoveryPageEntriesAction.php`
- Modify: `packages/seo-suite/src/Actions/SyncAiDiscoveryPageProfilesAction.php`
- Modify: `packages/seo-suite/tests/SeoSuiteTestCase.php`
- Modify: `packages/seo-suite/tests/Integration/Sitemap/NoIndexPageDiscoveryTest.php` or move assertion into AI Discovery tests

- [ ] **Step 1: Update SEO Suite dependencies**

In `packages/seo-suite/composer.json`, replace the sitemap library dependency with Site Discovery:

```json
"capell-app/site-discovery": "*",
```

The `require` section should include:

```json
"capell-app/admin": "*",
"capell-app/frontend": "*",
"capell-app/insights": "*",
"capell-app/site-discovery": "*",
"prism-php/prism": "^0.100"
```

In `packages/seo-suite/capell.json`, add `capell-app/site-discovery` to `dependencies.requires`:

```json
"capell-app/site-discovery"
```

Update the description to:

```json
"description": "SEO Suite adds metadata panels, structured data, broken link tracking, Search Console insights, AI-assisted content briefs, AI Discovery output, crawler policy controls, and publish checks."
```

- [ ] **Step 2: Remove sitemap registrations from SEO Suite provider**

In `packages/seo-suite/src/Providers/SeoSuiteServiceProvider.php`, remove imports for:

```php
use Capell\Admin\Contracts\AdminTools\AdminToolItem;
use Capell\Admin\Contracts\Extenders\ResourceHeaderActionExtender;
use Capell\Admin\Contracts\Extenders\SiteHeaderActionExtender;
use Capell\Admin\Contracts\Extenders\SiteRecordActionExtender;
use Capell\Core\Actions\RegisterBlazeOptimizedViewsAction;
use Capell\Core\Enums\TypeEnum;
use Capell\Core\Events\SiteCreated;
use Capell\Core\Models\Site;
use Capell\Core\Models\Blueprint;
use Capell\SeoSuite\Console\Commands\XmlSitemapCommand;
use Capell\SeoSuite\Filament\Extenders\Page\SitemapResourceHeaderActionExtender;
use Capell\SeoSuite\Filament\Extenders\Site\SitemapSiteHeaderActionExtender;
use Capell\SeoSuite\Filament\Extenders\Site\SitemapSiteRecordActionExtender;
use Capell\SeoSuite\Filament\Pages\SitemapPage;
use Capell\SeoSuite\Listeners\Sitemap\RegenerateSitemapsOnPageDeleted;
use Capell\SeoSuite\Listeners\Sitemap\RegenerateSitemapsOnPageSaved;
use Capell\SeoSuite\Listeners\Sitemap\RegenerateSitemapsOnSiteCreated;
use Capell\SeoSuite\Livewire\Page\Sitemap as SitemapLivewireComponent;
use Capell\SeoSuite\Livewire\Tools\SitemapTool;
use Capell\SeoSuite\Support\AdminTools\SitemapAdminTool;
use Capell\SeoSuite\Support\Creator\SitemapPageCreator;
use Capell\SeoSuite\Support\Interceptors\SitemapPageTypeInterceptor;
use Capell\SeoSuite\Support\Sitemap\Pages\PagesSitemap;
use Capell\SeoSuite\Support\Sitemap\SitemapPageRegistry;
use Capell\SeoSuite\Support\Sitemap\SitemapPageType;
```

Remove `XmlSitemapCommand::class` from `hasCommands()`.

Remove sitemap classes from `registerAdminExtenders()`, leaving only:

```php
$this->app->tag([
    AiCreatorSiteExtender::class,
], SiteHeaderActionExtender::TAG);
```

If `SiteHeaderActionExtender` is no longer needed after that edit, keep the import because `AiCreatorSiteExtender` still uses that tag. Remove `ResourceHeaderActionExtender`, `SiteRecordActionExtender`, and `AdminToolItem` if they have no remaining usage.

Remove `SitemapPage::class` from `registerFilamentPages()`.

Remove these methods from the provider:

```php
registerBlazeComponents()
registerLivewireComponents()
registerSitemapPageType()
registerSitemapDefaultPage()
registerSitemapRegistry()
registerSitemapEventListeners()
```

Remove these chained calls from `bootInstalledPackage()`:

```php
->registerBlazeComponents()
->registerSitemapPageType()
->registerSitemapDefaultPage()
->registerSitemapRegistry()
->registerSitemapEventListeners()
->registerLivewireComponents()
```

- [ ] **Step 3: Use Site Discovery in AI Discovery actions**

In `packages/seo-suite/src/Actions/SyncAiDiscoveryPageProfilesAction.php`, replace:

```php
use Capell\Core\Models\Page;
use Capell\SeoSuite\Support\Sitemap\Queries\PagesForSitemap;
```

with:

```php
use Capell\SiteDiscovery\Actions\DiscoverPublicPagesAction;
use Capell\SiteDiscovery\Data\DiscoverablePageData;
```

Replace:

```php
$pages = resolve(PagesForSitemap::class)->get($site, $language);

return $pages
    ->map(fn (Page $page): AiDiscoveryPageProfile => $this->resolvePageProfile($page, $site, $language, $siteProfile))
    ->values();
```

with:

```php
$pages = DiscoverPublicPagesAction::run($site, $language);

return $pages
    ->map(fn (DiscoverablePageData $page): AiDiscoveryPageProfile => $this->resolvePageProfile($page, $site, $language, $siteProfile))
    ->values();
```

Change `resolvePageProfile()` to accept `DiscoverablePageData` and use its page model:

```php
private function resolvePageProfile(
    DiscoverablePageData $page,
    Site $site,
    Language $language,
    AiDiscoverySiteProfile $siteProfile,
): AiDiscoveryPageProfile {
    throw_unless($page->page instanceof Page, LogicException::class, 'Discoverable page data did not include a page model.');

    return ResolveAiDiscoveryPageProfileAction::run($page->page, $site, $language, $siteProfile);
}
```

Keep `use Capell\Core\Models\Page;` because the guard references it.

In `packages/seo-suite/src/Actions/BuildAiDiscoveryPageEntriesAction.php`, replace `PagesForSitemap` with `DiscoverPublicPagesAction` and adjust helpers to use `DiscoverablePageData`. The entry method should start:

```php
private function entryForPage(
    DiscoverablePageData $discoverablePage,
    Collection $profiles,
    AiDiscoveryRenderContextData $context,
    AiDiscoverySiteProfile $siteProfile,
): ?AiDiscoveryPageEntryData {
    $page = $discoverablePage->page;

    if (! $page instanceof Page) {
        return null;
    }

    $profile = $profiles->get($page->getKey());
```

Update `profilesForPages()` to accept `Collection<int, DiscoverablePageData>` and use:

```php
->whereIn('page_id', $pages->pluck('pageId')->all())
```

- [ ] **Step 4: Update SEO Suite test case**

In `packages/seo-suite/tests/SeoSuiteTestCase.php`, add:

```php
use Capell\SiteDiscovery\Providers\SiteDiscoveryServiceProvider;
```

Remove:

```php
use Capell\SeoSuite\Filament\Pages\SitemapPage;
```

Add `SiteDiscoveryServiceProvider::class` before `SeoSuiteServiceProvider::class` in `getPackageProviders()`.

Force package installed:

```php
CapellCore::forcePackageInstalled(SiteDiscoveryServiceProvider::$packageName);
```

Remove:

```php
CapellAdmin::contributeToAdminSurface(AdminSurfaceContributionData::page(SitemapPage::class));
```

- [ ] **Step 5: Replace noindex AI Discovery test**

Move the AI Discovery assertion from `packages/seo-suite/tests/Integration/Sitemap/NoIndexPageDiscoveryTest.php` into a new file `packages/seo-suite/tests/Integration/AiDiscovery/AiDiscoveryUsesSiteDiscoveryTest.php`:

```php
<?php

declare(strict_types=1);

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\SeoSuite\Actions\GenerateLlmsTxtAction;
use Capell\SeoSuite\Data\AiDiscoveryRenderContextData;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
});

it('excludes non-discoverable pages from llms txt through site discovery', function (): void {
    $language = Language::query()->create([
        'name' => 'English',
        'locale' => 'en',
        'code' => 'en',
        'flag' => 'gb-eng',
        'status' => true,
        'default' => true,
        'order' => 1,
    ]);
    $site = Site::factory()->language($language)->withTranslations($language)->create();

    Page::factory()
        ->site($site)
        ->withTranslations($language, ['title' => 'Public Page'])
        ->create();

    Page::factory()
        ->site($site)
        ->withTranslations($language, ['title' => 'Private Page'])
        ->meta('robots', ['noindex'])
        ->create();

    $llmsTxt = GenerateLlmsTxtAction::run($site, $language);
    $contextLlmsTxt = GenerateLlmsTxtAction::run(new AiDiscoveryRenderContextData($site, $language, $site->siteDomains()->first()));

    expect($llmsTxt)->toContain('Public Page')
        ->and($llmsTxt)->not->toContain('Private Page')
        ->and($contextLlmsTxt)->toContain('Public Page')
        ->and($contextLlmsTxt)->not->toContain('Private Page');
});
```

Delete `packages/seo-suite/tests/Integration/Sitemap/NoIndexPageDiscoveryTest.php` after the Site Discovery discovery test covers the page query.

- [ ] **Step 6: Run SEO Suite AI Discovery tests**

```bash
vendor/bin/pest packages/seo-suite/tests/Integration/AiDiscovery packages/seo-suite/tests/Feature/Filament/AiDiscoveryPageTest.php --configuration=phpunit.xml
```

Expected: pass.

- [ ] **Step 7: Commit SEO Suite dependency update**

```bash
git add packages/seo-suite
git commit -m "refactor(seo-suite): depend on site discovery"
```

---

### Task 5: Update Package Docs And Metadata

**Files:**

- Modify: `packages/seo-suite/README.md`
- Modify: `packages/seo-suite/docs/overview.md`
- Modify: `packages/seo-suite/docs/sitemaps.md`
- Modify: `docs/README.md`
- Modify: `docs/openai-integration.md`
- Modify: `packages/diagnostics/src/Actions/Dashboard/BuildPackagesInstalledAction.php`
- Modify: `packages/agent-bridge/tests/Unit/KnowledgeRepositoryTest.php` if package recommendations assert package counts/metadata.
- Modify: `packages/blog/README.md`
- Modify: `packages/blog/capell.json`

- [ ] **Step 1: Remove sitemap ownership language from SEO Suite README**

In `packages/seo-suite/README.md`, replace the opening feature list with:

```markdown
SEO Suite adds metadata panels, structured data, broken link tracking, Search Console insights, AI-assisted content briefs, AI Discovery output, crawler policy controls, and publish checks.

- Page and site SEO schema extenders.
- SEO audit, AI Discovery, broken links, not-found URLs, and translation coverage pages.
- AI creator actions for briefs, images, layouts, metadata suggestions, and draft application.
- AI Discovery for `llms.txt`, optional `llms-full.txt`, page Markdown URLs, `Accept: text/markdown`, configurable AI crawler rules, and page-readiness audits.
- Search Console sync and dashboard-dashboard_reports.
```

Remove the PHP Sitemap Generator preview card and move that credit to `packages/site-discovery/README.md`.

In the install impact section, replace:

```markdown
- Adds sitemap, llms.txt, llms-full.txt, robots.txt, and page Markdown frontend output.
```

with:

```markdown
- Adds llms.txt, llms-full.txt, robots.txt, and page Markdown frontend output.
- Requires Site Discovery for public page discovery and sitemap outputs.
```

Remove the `capell:xml-sitemap` command from the SEO Suite commands list.

- [ ] **Step 2: Update SEO Suite docs**

In `packages/seo-suite/docs/overview.md`, replace direct sitemap ownership wording with:

```markdown
SEO Suite relies on Site Discovery for public page discovery and sitemap outputs.
```

In `packages/seo-suite/docs/sitemaps.md`, replace the file content with a short redirect-style document:

```markdown
# Sitemaps

Sitemap output now belongs to Site Discovery (`capell-app/site-discovery`).

SEO Suite depends on Site Discovery for the public discoverable-page API used by AI Discovery, but it no longer owns XML sitemap generation, the HTML sitemap page, or sitemap admin tooling.

See [../site-discovery/README.md](../site-discovery/README.md).
```

- [ ] **Step 3: Add Site Discovery to package docs index**

In `docs/README.md`, add a row near SEO Suite/Search:

```markdown
| Site Discovery | [`packages/site-discovery/README.md`](../packages/site-discovery/README.md) |
```

In `docs/openai-integration.md`, replace the Sitemaps link:

```markdown
- [Site Discovery](../packages/site-discovery/README.md)
```

- [ ] **Step 4: Add diagnostics package metadata**

In `packages/diagnostics/src/Actions/Dashboard/BuildPackagesInstalledAction.php`, add:

```php
'capell-app/site-discovery' => [
    'short' => 'site-discovery',
    'config' => null,
    'docs' => 'https://github.com/capell-app/capell-packages/blob/4.x/packages/site-discovery/README.md',
],
```

Use the exact array shape already used by nearby package entries. If `config` cannot be null in the local structure, omit it only if other entries omit it.

- [ ] **Step 5: Update blog wording**

In `packages/blog/README.md` and `packages/blog/capell.json`, replace "sitemaps" with "Site Discovery sitemap contributions" where Blog describes integration. If Blog currently imports `Capell\SeoSuite\Support\Sitemap`, the implementation task must update those imports to Site Discovery and add `capell-app/site-discovery` as the dependency instead of SEO Suite.

- [ ] **Step 6: Run docs/package metadata tests**

```bash
vendor/bin/pest packages/agent-bridge/tests/Unit/KnowledgeRepositoryTest.php packages/diagnostics/tests --configuration=phpunit.xml
```

Expected: pass after metadata fixture updates.

- [ ] **Step 7: Commit docs and metadata**

```bash
git add docs packages/seo-suite/README.md packages/seo-suite/docs packages/site-discovery/README.md packages/diagnostics packages/agent-bridge packages/blog
git commit -m "docs: document site discovery split"
```

---

### Task 6: Tighten Package Boundaries

**Files:**

- Modify: `packages/seo-suite/tests/Arch/SeoSuiteBoundaryTest.php`
- Modify: `packages/site-discovery/tests/Arch/SiteDiscoveryBoundaryTest.php`
- Modify: package imports found by `rg`

- [ ] **Step 1: Add SEO Suite sitemap-internal guard**

In `packages/seo-suite/tests/Arch/SeoSuiteBoundaryTest.php`, add:

```php
it('uses only site discovery public discovery APIs', function (): void {
    $packagePath = dirname(__DIR__, 2);
    $allowedPrefixes = [
        'Capell\\SiteDiscovery\\Actions\\DiscoverPublicPagesAction',
        'Capell\\SiteDiscovery\\Actions\\DiscoverPublicUrlsAction',
        'Capell\\SiteDiscovery\\Contracts\\DiscoverableUrlSource',
        'Capell\\SiteDiscovery\\Data\\DiscoverablePageData',
        'Capell\\SiteDiscovery\\Data\\DiscoverableUrlData',
        'Capell\\SiteDiscovery\\Providers\\SiteDiscoveryServiceProvider',
    ];
    $violations = [];

    foreach ((new Symfony\Component\Finder\Finder)->files()->in($packagePath . '/src')->name('*.php') as $file) {
        $contents = $file->getContents();

        if (! str_contains($contents, 'Capell\\SiteDiscovery\\')) {
            continue;
        }

        foreach ($allowedPrefixes as $allowedPrefix) {
            if (str_contains($contents, $allowedPrefix)) {
                continue 2;
            }
        }

        $violations[] = str_replace($packagePath . '/', '', $file->getPathname());
    }

    expect($violations)->toBeEmpty();
});
```

If multiple allowed imports appear in one file, this test still passes. It fails when a file imports `Capell\SiteDiscovery\Support\Sitemap`.

- [ ] **Step 2: Add Site Discovery reverse dependency guard**

In `packages/site-discovery/tests/Arch/SiteDiscoveryBoundaryTest.php`, keep SEO Suite forbidden:

```php
arch('site-discovery does not import seo suite')
    ->expect('Capell\SiteDiscovery')
    ->not->toUse('Capell\SeoSuite');
```

- [ ] **Step 3: Search and fix invalid imports**

Run:

```bash
rg -n "Capell\\\\SeoSuite\\\\.*Sitemap|Capell\\\\SeoSuite\\\\Support\\\\Sitemap|PagesForSitemap|Capell\\\\SiteDiscovery\\\\Support\\\\Sitemap" packages/seo-suite packages/site-discovery packages/blog packages/events packages/tags
```

Expected after fixes:

- No `Capell\SeoSuite\...\Sitemap` imports outside historical docs.
- No `PagesForSitemap`.
- No `Capell\SiteDiscovery\Support\Sitemap` imports from SEO Suite.

- [ ] **Step 4: Run boundary tests**

```bash
vendor/bin/pest packages/site-discovery/tests/Arch packages/seo-suite/tests/Arch --configuration=phpunit.xml
```

Expected: pass.

- [ ] **Step 5: Commit boundary hardening**

```bash
git add packages/site-discovery/tests/Arch packages/seo-suite/tests/Arch packages/blog packages/events packages/tags
git commit -m "test: enforce site discovery package boundaries"
```

---

### Task 7: Final Verification

**Files:**

- No planned source edits unless verification exposes failures.

- [ ] **Step 1: Regenerate autoload**

```bash
COMPOSER=composer.local.json composer dump-autoload --no-scripts
```

Expected: succeeds with Site Discovery and SEO Suite namespaces loaded.

- [ ] **Step 2: Run narrow package suites**

```bash
vendor/bin/pest packages/site-discovery/tests --configuration=phpunit.xml
vendor/bin/pest packages/seo-suite/tests --configuration=phpunit.xml
```

Expected: both pass.

- [ ] **Step 3: Run affected package suites**

```bash
vendor/bin/pest packages/blog/tests packages/events/tests packages/tags/tests packages/agent-bridge/tests packages/diagnostics/tests --configuration=phpunit.xml
```

Expected: pass or expose only pre-existing unrelated failures. Any real import/dependency failure from the split must be fixed before continuing.

- [ ] **Step 4: Run static analysis on affected source**

```bash
composer analyze
```

Expected: pass. If analysis is too broad or blocked by unrelated dirty-tree work, run the narrowest PHPStan command available for `packages/site-discovery` and `packages/seo-suite`, then record the broader blocker in the final implementation notes.

- [ ] **Step 5: Run changed-file preflight**

```bash
composer preflight
```

Expected: pass.

- [ ] **Step 6: Final grep checks**

```bash
rg -n "PagesForSitemap|Capell\\\\SeoSuite\\\\.*Sitemap|capell-seo-suite::livewire\\.page\\.sitemap|capell-seo-suite::livewire\\.tools\\.sitemap-tool" packages docs
rg -n "icamys/php-sitemap-generator" packages/seo-suite packages/site-discovery composer.json composer.local.json
```

Expected:

- First command has no production-code matches.
- Second command shows `icamys/php-sitemap-generator` in Site Discovery/root composer files, not SEO Suite package composer.

- [ ] **Step 7: Commit final fixes**

```bash
git add composer.json composer.local.json packages docs
git commit -m "chore: verify site discovery extraction"
```

Only create this commit if final verification required additional fixes. If no files changed after prior commits, do not create an empty commit.

---

## Self-Review

Spec coverage:

- New package: covered by Tasks 1-3.
- SEO Suite depends on Site Discovery: covered by Task 4.
- Neutral discovery API instead of sitemap internals: covered by Tasks 2, 4, and 6.
- Docs and metadata: covered by Task 5.
- Tests and verification: covered by Tasks 2-7.

Placeholder scan:

- The plan uses concrete filenames, commands, test names, API names, and code snippets.
- No deferred requirements remain for the implementation worker.

Type consistency:

- Public API names match the design spec: `DiscoverPublicPagesAction`, `DiscoverPublicUrlsAction`, `DiscoverablePageData`, `DiscoverableUrlData`, `DiscoverableUrlSource`.
- Site Discovery provider namespace is `Capell\SiteDiscovery\Providers\SiteDiscoveryServiceProvider`.
- Package service name is `capell-site-discovery`; package composer name is `capell-app/site-discovery`.
