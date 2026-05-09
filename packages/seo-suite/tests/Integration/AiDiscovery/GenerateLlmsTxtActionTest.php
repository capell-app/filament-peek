<?php

declare(strict_types=1);

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Frontend\Support\State\FrontendState;
use Capell\SeoSuite\Actions\GenerateLlmsTxtAction;
use Capell\SeoSuite\Actions\PersistAiDiscoverySnapshotAction;
use Capell\SeoSuite\Actions\ResolveAiDiscoveryProfileAction;
use Capell\SeoSuite\Data\AiDiscoveryRenderContextData;
use Capell\SeoSuite\Enums\AiDiscoverySnapshotKindEnum;
use Capell\SeoSuite\Enums\AiDiscoveryStatusEnum;
use Capell\SeoSuite\Http\Controllers\LlmsTxtController;
use Capell\SeoSuite\Models\AiDiscoveryPageProfile;
use Capell\SeoSuite\Models\AiDiscoverySnapshot;
use Capell\SeoSuite\Tests\Support\AiDiscoveryIntegrationTestCase;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

$composerAutoloader = require getcwd() . '/vendor/autoload.php';

if ($composerAutoloader instanceof ClassLoader) {
    $packageRoot = dirname(__DIR__, 3);

    $composerAutoloader->addPsr4('Capell\\SeoSuite\\', $packageRoot . '/src');
    $composerAutoloader->addPsr4('Capell\\SeoSuite\\Database\\Factories\\', $packageRoot . '/database/factories');
    $composerAutoloader->addPsr4('Capell\\SeoSuite\\Tests\\', $packageRoot . '/tests');
}

require_once dirname(__DIR__, 2) . '/Support/AiDiscoveryIntegrationTestCase.php';

uses(AiDiscoveryIntegrationTestCase::class);

beforeEach(function (): void {
    Cache::flush();
    Carbon::setTestNow();
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function createAiDiscoveryLanguage(): Language
{
    return Language::query()->create([
        'name' => 'English',
        'locale' => 'en',
        'code' => 'en',
        'flag' => 'gb-eng',
        'status' => true,
        'default' => true,
        'order' => 1,
    ]);
}

it('groups llms txt entries by section and orders by priority', function (): void {
    $language = createAiDiscoveryLanguage();
    $site = Site::factory()->language($language)->withTranslations($language)->create();
    $firstPage = Page::factory()
        ->site($site)
        ->withTranslations($language, [
            'title' => 'First Page',
            'meta' => ['description' => 'SEO fallback'],
        ])
        ->create();
    $secondPage = Page::factory()
        ->site($site)
        ->withTranslations($language, ['title' => 'Second Page'])
        ->create();

    ResolveAiDiscoveryProfileAction::run($site, $language, $firstPage);
    ResolveAiDiscoveryProfileAction::run($site, $language, $secondPage);
    AiDiscoveryPageProfile::query()->where('page_id', $firstPage->getKey())->update([
        'summary' => 'First AI summary',
        'section' => 'Guides',
        'priority' => 200,
    ]);
    AiDiscoveryPageProfile::query()->where('page_id', $secondPage->getKey())->update([
        'summary' => 'Second AI summary',
        'section' => 'Guides',
        'priority' => 100,
    ]);

    $content = GenerateLlmsTxtAction::run($site, $language);

    expect($content)->toStartWith('# ')
        ->and($content)->toContain('## Guides')
        ->and($content)->toContain('Second AI summary')
        ->and($content)->toContain('First AI summary')
        ->and($content)->not->toContain('SEO fallback')
        ->and(strpos($content, 'Second Page'))->toBeLessThan(strpos($content, 'First Page'));
});

it('falls back to canonical page url when markdown pages are disabled', function (): void {
    $language = createAiDiscoveryLanguage();
    $site = Site::factory()->language($language)->withTranslations($language)->create();
    Page::factory()->site($site)->withTranslations($language, ['title' => 'About'])->create();
    $profile = ResolveAiDiscoveryProfileAction::run($site, $language);
    $profile->update(['markdown_pages_enabled' => false]);

    $content = GenerateLlmsTxtAction::run($site, $language);

    expect($content)->toContain('](http')
        ->and($content)->not->toContain('.md)');
});

it('uses markdown page urls when markdown pages are enabled', function (): void {
    $language = createAiDiscoveryLanguage();
    $site = Site::factory()->language($language)->withTranslations($language)->create();
    Page::factory()->site($site)->withTranslations($language, ['title' => 'About'])->create();
    ResolveAiDiscoveryProfileAction::run($site, $language);

    $content = GenerateLlmsTxtAction::run($site, $language);

    expect($content)->toContain('.md)');
});

it('returns empty content when llms txt is disabled', function (): void {
    $language = createAiDiscoveryLanguage();
    $site = Site::factory()->language($language)->withTranslations($language)->create();
    Page::factory()->site($site)->withTranslations($language, ['title' => 'About'])->create();
    $profile = ResolveAiDiscoveryProfileAction::run($site, $language);
    $profile->update(['llms_txt_enabled' => false]);

    $content = GenerateLlmsTxtAction::run($site, $language);

    expect($content)->toBe('');
});

it('persists ai discovery snapshots by context key', function (): void {
    $language = createAiDiscoveryLanguage();
    $site = Site::factory()->language($language)->withTranslations($language)->create();
    $siteDomain = $site->siteDomains()->first();
    $context = new AiDiscoveryRenderContextData($site, $language, $siteDomain);

    $snapshot = PersistAiDiscoverySnapshotAction::run(
        context: $context,
        kind: AiDiscoverySnapshotKindEnum::LlmsTxt,
        content: '# Site',
        cacheKey: 'capell-seo-suite:ai-discovery:1:default:1:llms_txt',
        ttlSeconds: 3600,
    );

    $updatedSnapshot = PersistAiDiscoverySnapshotAction::run(
        context: $context,
        kind: AiDiscoverySnapshotKindEnum::LlmsTxt,
        content: '# Updated Site',
        cacheKey: 'capell-seo-suite:ai-discovery:1:default:1:llms_txt',
        ttlSeconds: 1800,
        status: AiDiscoveryStatusEnum::Stale->value,
    );

    expect($updatedSnapshot->is($snapshot))->toBeTrue()
        ->and(AiDiscoverySnapshot::query()->count())->toBe(1)
        ->and($updatedSnapshot->context_key)->toBe($context->domainKey() . ':site')
        ->and($updatedSnapshot->site_domain_id)->toBe($siteDomain?->getKey())
        ->and($updatedSnapshot->content_hash)->toBe(hash('sha256', '# Updated Site'))
        ->and($updatedSnapshot->byte_size)->toBe(strlen('# Updated Site'))
        ->and($updatedSnapshot->status)->toBe(AiDiscoveryStatusEnum::Stale)
        ->and($updatedSnapshot->expires_at)->not->toBeNull();
});

it('serves llms txt with markdown headers and only persists snapshots on cache misses', function (): void {
    Carbon::setTestNow(Carbon::parse('2026-05-09 12:00:00'));

    $language = createAiDiscoveryLanguage();
    $site = Site::factory()->language($language)->withTranslations($language)->create();
    $siteDomain = $site->siteDomains()->first();
    Page::factory()->site($site)->withTranslations($language, ['title' => 'About'])->create();
    $profile = ResolveAiDiscoveryProfileAction::run($site, $language);
    $profile->update(['cache_ttl_seconds' => 3600]);

    app(FrontendState::class)
        ->withSite($site)
        ->withLanguage($language)
        ->withDomain($siteDomain);

    $firstResponse = app(LlmsTxtController::class)();
    $snapshot = AiDiscoverySnapshot::query()->sole();
    $generatedAt = $snapshot->generated_at?->copy();
    $cacheKey = sprintf(
        'capell-seo-suite:ai-discovery:%d:%s:%d:llms_txt',
        $site->getKey(),
        (new AiDiscoveryRenderContextData($site, $language, $siteDomain))->domainKey(),
        $language->getKey(),
    );

    Carbon::setTestNow(Carbon::parse('2026-05-09 12:10:00'));

    $secondResponse = app(LlmsTxtController::class)();
    $snapshot->refresh();

    expect($firstResponse->getStatusCode())->toBe(200)
        ->and($firstResponse->headers->get('Content-Type'))->toBe('text/markdown; charset=utf-8')
        ->and($firstResponse->headers->getCacheControlDirective('public'))->toBeTrue()
        ->and($firstResponse->headers->getCacheControlDirective('max-age'))->toBe('3600')
        ->and($firstResponse->headers->get('ETag'))->toBe('"' . hash('sha256', $firstResponse->getContent()) . '"')
        ->and(Cache::has($cacheKey))->toBeTrue()
        ->and($secondResponse->getContent())->toBe($firstResponse->getContent())
        ->and(AiDiscoverySnapshot::query()->count())->toBe(1)
        ->and($snapshot->cache_key)->toBe($cacheKey)
        ->and($snapshot->generated_at?->equalTo($generatedAt))->toBeTrue();
});
