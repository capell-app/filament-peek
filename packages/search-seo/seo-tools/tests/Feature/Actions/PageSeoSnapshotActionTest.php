<?php

declare(strict_types=1);

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\SeoTools\Actions\PersistPageSeoSnapshotAction;
use Capell\SeoTools\Data\InternalLinkSuggestionData;
use Capell\SeoTools\Data\PageSeoReportData;
use Capell\SeoTools\Data\RedirectOpportunityData;
use Capell\SeoTools\Data\SeoIssueData;
use Capell\SeoTools\Data\SeoPreviewData;
use Capell\SeoTools\Enums\SeoCheckKeyEnum;
use Capell\SeoTools\Enums\SeoIssueSeverityEnum;
use Capell\SeoTools\Models\PageSeoSnapshot;

it('upserts a compact page seo snapshot from a report', function (): void {
    $language = Language::factory()->create();
    $site = Site::factory()->language($language)->withTranslations($language)->create();
    $page = Page::factory()->site($site)->withTranslations($language)->create();

    $report = new PageSeoReportData(
        score: 65,
        searchPreview: new SeoPreviewData(
            title: 'About Capell',
            description: 'A search preview description.',
            url: 'https://example.com/about',
        ),
        socialPreview: new SeoPreviewData(
            title: 'About Capell',
            description: 'A social preview description.',
            url: 'https://example.com/about',
        ),
        issues: [
            new SeoIssueData(
                key: SeoCheckKeyEnum::MetaTitle,
                severity: SeoIssueSeverityEnum::Critical,
                message: 'Meta title is missing.',
            ),
            new SeoIssueData(
                key: SeoCheckKeyEnum::Schema,
                severity: SeoIssueSeverityEnum::Warning,
                message: 'Schema is missing.',
            ),
        ],
        passedChecks: [
            SeoCheckKeyEnum::MetaDescription,
        ],
        internalLinkSuggestions: [
            new InternalLinkSuggestionData(
                pageId: (int) $page->getKey(),
                title: 'Related page',
                url: 'https://example.com/related',
                score: 80,
                reason: 'Relevant topic overlap.',
            ),
        ],
        redirectOpportunities: [
            new RedirectOpportunityData(
                sourceUrl: 'https://example.com/old-one',
                hits: 12,
                siteId: (int) $site->getKey(),
                languageId: (int) $language->getKey(),
                suggestedTargetUrl: 'https://example.com/about',
                pageName: 'About Capell',
            ),
            new RedirectOpportunityData(
                sourceUrl: 'https://example.com/old-two',
                hits: 8,
                siteId: (int) $site->getKey(),
                languageId: (int) $language->getKey(),
                suggestedTargetUrl: 'https://example.com/about',
                pageName: 'About Capell',
            ),
        ],
    );

    $firstSnapshot = PersistPageSeoSnapshotAction::run($page, $site, $language, $report);
    $secondSnapshot = PersistPageSeoSnapshotAction::run($page, $site, $language, $report);

    expect(PageSeoSnapshot::query()->count())->toBe(1)
        ->and($secondSnapshot->is($firstSnapshot))->toBeTrue()
        ->and($secondSnapshot->score)->toBe(65)
        ->and($secondSnapshot->critical_count)->toBe(1)
        ->and($secondSnapshot->warning_count)->toBe(1)
        ->and($secondSnapshot->issue_keys)->toBe(['meta_title', 'schema'])
        ->and($secondSnapshot->passed_check_keys)->toBe(['meta_description'])
        ->and($secondSnapshot->redirect_opportunities_count)->toBe(2)
        ->and($secondSnapshot->computed_at)->not()->toBeNull();
});
