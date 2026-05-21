<?php

declare(strict_types=1);

use Capell\SeoSuite\Data\InternalLinkSuggestionData;
use Capell\SeoSuite\Data\PageSeoReportData;
use Capell\SeoSuite\Data\RedirectOpportunityData;
use Capell\SeoSuite\Data\SchemaTemplateReportData;
use Capell\SeoSuite\Data\SearchConsoleInsightData;
use Capell\SeoSuite\Data\SeoIssueData;
use Capell\SeoSuite\Data\SeoPreviewData;
use Capell\SeoSuite\Data\SocialMetaData;
use Capell\SeoSuite\Enums\OpenGraphTypeEnum;
use Capell\SeoSuite\Enums\SchemaTemplateTypeEnum;
use Capell\SeoSuite\Enums\SearchConsoleMetricEnum;
use Capell\SeoSuite\Enums\SeoCheckKeyEnum;
use Capell\SeoSuite\Enums\SeoIssueSeverityEnum;

it('summarizes seo report issues by severity and check key', function (): void {
    $critical = new SeoIssueData(
        key: SeoCheckKeyEnum::MetaTitle,
        severity: SeoIssueSeverityEnum::Critical,
        message: 'Missing meta title.',
    );
    $warning = new SeoIssueData(
        key: SeoCheckKeyEnum::MetaDescription,
        severity: SeoIssueSeverityEnum::Warning,
        message: 'Meta description is short.',
        actionLabel: 'Edit SEO',
        actionUrl: '/admin/pages/1/edit',
    );
    $notice = new SeoIssueData(
        key: SeoCheckKeyEnum::InternalLinks,
        severity: SeoIssueSeverityEnum::Notice,
        message: 'Add internal links.',
    );
    $report = new PageSeoReportData(
        score: 62,
        searchPreview: new SeoPreviewData('Title', 'Description', 'https://capell.test'),
        socialPreview: new SeoPreviewData('Social title', 'Social description', 'https://capell.test', 'https://capell.test/social.jpg'),
        issues: [$critical, $warning, $notice],
        passedChecks: [SeoCheckKeyEnum::Canonical, $notice, 'schema'],
        canonicalUrl: 'https://capell.test',
        robotsDirectives: ['index', 'follow'],
    );

    expect($report->criticalCount())->toBe(1)
        ->and($report->warningCount())->toBe(1)
        ->and($report->issuesBySeverity(SeoIssueSeverityEnum::Notice))->toBe([$notice])
        ->and($report->issuesForKey(SeoCheckKeyEnum::MetaDescription))->toBe([$warning])
        ->and($report->hasIssuesForKey(SeoCheckKeyEnum::MetaTitle))->toBeTrue()
        ->and($report->hasIssuesForKey(SeoCheckKeyEnum::Schema))->toBeFalse()
        ->and($report->passedCheckValues())->toBe(['canonical', 'internal_links', 'schema']);
});

it('carries seo dashboard report value data', function (): void {
    $suggestion = new InternalLinkSuggestionData(
        pageId: 10,
        title: 'Pricing',
        url: '/pricing',
        score: 87,
        reason: 'Relevant pricing intent.',
    );
    $schemaReport = new SchemaTemplateReportData(
        templateType: SchemaTemplateTypeEnum::Article,
        presentFields: ['headline', 'author'],
        missingFields: ['datePublished'],
        severity: SeoIssueSeverityEnum::Warning,
    );
    $redirect = new RedirectOpportunityData(
        sourceUrl: '/old',
        hits: 12,
        siteId: 1,
        languageId: 2,
        suggestedTargetUrl: '/new',
        pageName: 'New page',
    );
    $searchConsoleInsight = new SearchConsoleInsightData(
        metric: SearchConsoleMetricEnum::Clicks,
        message: 'Clicks declined.',
        value: 10,
        previousValue: 20,
        delta: -50.0,
        severity: SeoIssueSeverityEnum::Warning,
    );
    $social = new SocialMetaData(
        title: 'Capell',
        description: 'CMS platform',
        imageUrl: 'https://capell.test/social.jpg',
        imageWidth: 1200,
        imageHeight: 630,
        imageMimeType: 'image/jpeg',
        imageAlt: 'Capell CMS',
        ogType: OpenGraphTypeEnum::Article,
        url: 'https://capell.test/blog',
        locale: 'en_GB',
        siteName: 'Capell',
        twitterHandle: '@capell',
    );

    expect($suggestion->score)->toBe(87)
        ->and($schemaReport->templateType)->toBe(SchemaTemplateTypeEnum::Article)
        ->and($schemaReport->missingFields)->toBe(['datePublished'])
        ->and($redirect->suggestedTargetUrl)->toBe('/new')
        ->and($searchConsoleInsight->metric)->toBe(SearchConsoleMetricEnum::Clicks)
        ->and($searchConsoleInsight->delta)->toBe(-50.0)
        ->and($social->ogType)->toBe(OpenGraphTypeEnum::Article)
        ->and($social->twitterCard)->toBe('summary_large_image');
});
