<?php

declare(strict_types=1);

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\PageUrl;
use Capell\Core\Models\Site;
use Capell\Core\Models\Translation;
use Capell\SeoSuite\Support\Publishing\SeoPublishReportProviderAdapter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

it('resolves publish report languages from page translations before falling back to the site language', function (): void {
    $english = new Language;
    $english->forceFill(['id' => 10, 'name' => 'English']);

    $french = new Language;
    $french->forceFill(['id' => 20, 'name' => 'French']);

    $site = new Site;
    $site->setRelation('language', $english);

    $englishTranslation = new Translation;
    $englishTranslation->setRelation('language', $english);

    $duplicateEnglishTranslation = new Translation;
    $duplicateEnglishTranslation->setRelation('language', $english);

    $frenchTranslation = new Translation;
    $frenchTranslation->setRelation('language', $french);

    $page = new Page;
    $page->setRelation('translations', new EloquentCollection([
        $englishTranslation,
        $duplicateEnglishTranslation,
        $frenchTranslation,
    ]));

    $method = new ReflectionMethod(SeoPublishReportProviderAdapter::class, 'languagesForPage');
    $languages = $method->invoke(new SeoPublishReportProviderAdapter, $page, $site);

    expect($languages)->toHaveCount(2)
        ->and(array_map(
            fn (Language $language): int => (int) $language->getKey(),
            $languages,
        ))->toBe([10, 20]);
});

it('falls back to site language when a publish report page has no translation languages', function (): void {
    $english = new Language;
    $english->forceFill(['id' => 10, 'name' => 'English']);

    $site = new Site;
    $site->setRelation('language', $english);

    $page = new Page;
    $page->setRelation('translations', new EloquentCollection);

    $method = new ReflectionMethod(SeoPublishReportProviderAdapter::class, 'languagesForPage');
    $languages = $method->invoke(new SeoPublishReportProviderAdapter, $page, $site);

    expect($languages)->toHaveCount(1)
        ->and($languages[0])->toBe($english);
});

it('uses public urls before page names and uuids for publish report labels', function (): void {
    $pageUrl = new PageUrl;
    $pageUrl->forceFill(['url' => '/about']);

    $page = new Page;
    $page->forceFill([
        'id' => 123,
        'name' => 'About page',
        'uuid' => 'page-uuid',
    ]);
    $page->setRelation('pageUrls', new EloquentCollection([$pageUrl]));

    $method = new ReflectionMethod(SeoPublishReportProviderAdapter::class, 'pageLabel');

    expect($method->invoke(new SeoPublishReportProviderAdapter, $page))->toBe('/about');

    $page->setRelation('pageUrls', new EloquentCollection);

    expect($method->invoke(new SeoPublishReportProviderAdapter, $page))->toBe('About page');

    $page->forceFill(['name' => '   ']);

    expect($method->invoke(new SeoPublishReportProviderAdapter, $page))->toBe('page-uuid');
});

it('normalizes scalar publish report values and rejects empty or structured values', function (): void {
    $method = new ReflectionMethod(SeoPublishReportProviderAdapter::class, 'stringValue');
    $adapter = new SeoPublishReportProviderAdapter;

    expect($method->invoke($adapter, '  Search page  '))->toBe('Search page')
        ->and($method->invoke($adapter, 42))->toBe('42')
        ->and($method->invoke($adapter, '   '))->toBeNull()
        ->and($method->invoke($adapter, ['not' => 'scalar']))->toBeNull();
});
