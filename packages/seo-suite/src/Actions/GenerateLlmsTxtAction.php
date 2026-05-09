<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Actions;

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\Translation;
use Capell\SeoSuite\Data\AiDiscoveryPageEntryData;
use Capell\SeoSuite\Data\AiDiscoveryRenderContextData;
use Capell\SeoSuite\Models\AiDiscoveryPageProfile;
use Capell\SeoSuite\Models\AiDiscoverySiteProfile;
use Capell\SeoSuite\Support\Sitemap\Queries\PagesForSitemap;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use LogicException;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Generate llms.txt content for a site.
 *
 * @method static string run(AiDiscoveryRenderContextData|Site $context, ?Language $language = null)
 */
final class GenerateLlmsTxtAction
{
    use AsAction;

    public function handle(AiDiscoveryRenderContextData|Site $context, ?Language $language = null): string
    {
        $renderContext = $this->renderContext($context, $language);
        $siteProfile = ResolveAiDiscoveryProfileAction::run($renderContext->site, $renderContext->language);

        throw_unless($siteProfile instanceof AiDiscoverySiteProfile, LogicException::class, 'Resolving an AI Discovery site profile returned an unexpected page profile.');

        if (! $siteProfile->llms_txt_enabled) {
            return '';
        }

        SyncAiDiscoveryPageProfilesAction::run($renderContext->site, $renderContext->language);

        $entries = $this->getPageEntries($renderContext, $siteProfile);

        $lines = ['# ' . $this->siteTitle($renderContext)];

        if (is_string($siteProfile->intro_markdown) && trim($siteProfile->intro_markdown) !== '') {
            $lines[] = '';
            $lines[] = trim($siteProfile->intro_markdown);
        }

        foreach ($entries->groupBy('section') as $section => $sectionEntries) {
            $lines[] = '';
            $lines[] = '## ' . strip_tags((string) $section);

            foreach ($sectionEntries as $entry) {
                $lines[] = $entry->toLlmsTxtLine();
            }
        }

        return rtrim(implode("\n", $lines)) . "\n";
    }

    /**
     * @return Collection<int, AiDiscoveryPageEntryData>
     */
    private function getPageEntries(AiDiscoveryRenderContextData $context, AiDiscoverySiteProfile $siteProfile): Collection
    {
        $pages = resolve(PagesForSitemap::class)->get($context->site, $context->language);
        $profiles = $this->profilesForPages($context, $pages);

        return $pages
            ->map(fn (Page $page): ?AiDiscoveryPageEntryData => $this->entryForPage($page, $profiles, $context, $siteProfile))
            ->filter(fn (?AiDiscoveryPageEntryData $entry): bool => $entry instanceof AiDiscoveryPageEntryData)
            ->sortBy([
                ['section', 'asc'],
                ['priority', 'asc'],
            ])
            ->values();
    }

    /**
     * @param  EloquentCollection<int, Page>  $pages
     * @return Collection<int, AiDiscoveryPageProfile>
     */
    private function profilesForPages(AiDiscoveryRenderContextData $context, EloquentCollection $pages): Collection
    {
        return AiDiscoveryPageProfile::query()
            ->where('site_id', $context->site->getKey())
            ->where('language_id', $context->language->getKey())
            ->whereIn('page_id', $pages->pluck('id')->all())
            ->get()
            ->keyBy('page_id');
    }

    /**
     * @param  Collection<int, AiDiscoveryPageProfile>  $profiles
     */
    private function entryForPage(
        Page $page,
        Collection $profiles,
        AiDiscoveryRenderContextData $context,
        AiDiscoverySiteProfile $siteProfile,
    ): ?AiDiscoveryPageEntryData {
        $profile = $profiles->get($page->getKey());

        if (! $profile instanceof AiDiscoveryPageProfile || ! $profile->include_in_ai_index) {
            return null;
        }

        if ($context->siteDomain !== null && $page->pageUrl !== null) {
            $page->pageUrl->setRelation('siteDomain', $context->siteDomain);
        }

        $url = $page->pageUrl?->full_url ?? '';
        $title = trim(strip_tags($page->translation?->title ?? $page->translation?->label ?? ''));

        if ($url === '' || $title === '') {
            return null;
        }

        return new AiDiscoveryPageEntryData(
            title: $title,
            url: $url,
            markdownUrl: $siteProfile->markdown_pages_enabled ? $this->markdownUrl($url) : null,
            description: $this->description($profile, $page->translation),
            section: $this->section($profile, $siteProfile),
            priority: $profile->priority,
            pageId: (int) $page->getKey(),
        );
    }

    private function renderContext(AiDiscoveryRenderContextData|Site $context, ?Language $language): AiDiscoveryRenderContextData
    {
        if ($context instanceof AiDiscoveryRenderContextData) {
            return $context;
        }

        if (! $language instanceof Language) {
            throw new InvalidArgumentException('A language is required when generating llms.txt from a site.');
        }

        return new AiDiscoveryRenderContextData(site: $context, language: $language);
    }

    private function siteTitle(AiDiscoveryRenderContextData $context): string
    {
        return trim(strip_tags((string) $context->site->getMeta(
            'business_name',
            $context->site->translation?->title ?? config('app.name'),
        )));
    }

    private function description(AiDiscoveryPageProfile $profile, ?Translation $translation): ?string
    {
        $summary = trim((string) $profile->summary);

        if ($summary !== '') {
            return $summary;
        }

        $metaDescription = trim((string) $translation?->meta_description);

        if ($metaDescription !== '') {
            return $metaDescription;
        }

        $meta = (array) $translation?->meta;
        $description = trim((string) ($meta['description'] ?? ''));

        return $description !== '' ? $description : null;
    }

    private function section(AiDiscoveryPageProfile $profile, AiDiscoverySiteProfile $siteProfile): string
    {
        $section = trim($profile->section);

        if ($section !== '') {
            return $section;
        }

        return trim($siteProfile->default_section) !== ''
            ? $siteProfile->default_section
            : 'Pages';
    }

    private function markdownUrl(string $url): string
    {
        return mb_rtrim($url, '/') . '.md';
    }
}
