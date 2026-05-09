<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Tests\Unit\Data;

use Capell\PublishingStudio\Activity\WorkspaceActivityEntry;
use Capell\PublishingStudio\Approvals\RequiredReviewer;
use Capell\PublishingStudio\Checks\PublishCheckResult;
use Capell\PublishingStudio\Checks\PublishCheckSeverity;
use Capell\PublishingStudio\Data\Dashboard\MergeHistoryEntryData;
use Capell\PublishingStudio\Data\Dashboard\WorkspaceMergeData;
use Capell\PublishingStudio\Data\PagePublishStateData;
use Capell\PublishingStudio\DryRunReport;
use Capell\PublishingStudio\Enums\WorkspaceStatusEnum;
use Capell\PublishingStudio\Models\Workspace;
use Capell\PublishingStudio\RebaseReport;
use Carbon\CarbonImmutable;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PublishingStudioReportDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Container::getInstance()->instance('translator', new class
        {
            /**
             * @param  array<string, mixed>  $replace
             */
            public function get(string $key, array $replace = [], ?string $locale = null, bool $fallback = true): string
            {
                if ($replace === []) {
                    return $key;
                }

                return $key . ':' . json_encode($replace, JSON_THROW_ON_ERROR);
            }
        });
    }

    public function test_page_publish_state_reports_workspace_publication_and_label_state(): void
    {
        $publishedState = new PagePublishStateData(
            pageId: 10,
            isDraft: false,
            publishedAt: CarbonImmutable::parse('2026-06-01 09:00:00', 'UTC'),
            previewUrl: null,
            workspaceId: null,
            workspaceName: null,
            workspaceStatus: null,
        );

        $workspaceDraftState = new PagePublishStateData(
            pageId: 10,
            isDraft: true,
            publishedAt: null,
            previewUrl: 'https://example.test/preview',
            workspaceId: 7,
            workspaceName: 'Summer launch',
            workspaceStatus: WorkspaceStatusEnum::Open,
        );

        self::assertFalse($publishedState->hasActiveWorkspace());
        self::assertTrue($publishedState->isPublished());
        self::assertSame('capell-admin::publish_panel.status_published', $publishedState->statusLabel());
        self::assertTrue($workspaceDraftState->hasActiveWorkspace());
        self::assertFalse($workspaceDraftState->isPublished());
        self::assertSame(
            'capell-admin::publish_panel.status_draft_in_workspace:{"workspace":"Summer launch"}',
            $workspaceDraftState->statusLabel(),
        );
    }

    public function test_dry_run_report_summarises_rows_collisions_conflicts_and_blocking_checks(): void
    {
        $workspace = new Workspace;
        $workspace->id = 7;
        $workspace->base_version_id = 1;

        $rebaseReport = new RebaseReport(
            workspace: $workspace,
            currentLiveVersionId: 3,
            conflicts: [],
        );
        $rebaseReport->addConflict(Workspace::class, 'workspace-uuid');

        $report = new DryRunReport(
            workspace: $workspace,
            wouldPublish: false,
            rebaseReport: $rebaseReport,
            collisions: [
                ['site_id' => 1, 'language_id' => 1, 'url' => '/about'],
            ],
            rowCounts: [
                Workspace::class => 2,
                'App\\Models\\Page' => 3,
            ],
            failure: new RuntimeException('Publish blocked.'),
            checkResults: [
                new PublishCheckResult(
                    identifier: 'seo',
                    label: 'SEO',
                    severity: PublishCheckSeverity::Error,
                    messages: ['Missing title.'],
                ),
            ],
        );

        self::assertSame(5, $report->totalRows());
        self::assertTrue($report->hasCollisions());
        self::assertTrue($report->hasConflicts());
        self::assertTrue($report->hasBlockingCheckErrors());
    }

    public function test_dry_run_report_treats_clean_error_checks_as_non_blocking(): void
    {
        $report = new DryRunReport(
            workspace: new Workspace,
            wouldPublish: true,
            rebaseReport: null,
            collisions: [],
            rowCounts: [],
            checkResults: [
                new PublishCheckResult(
                    identifier: 'accessibility',
                    label: 'Accessibility',
                    severity: PublishCheckSeverity::Error,
                ),
            ],
        );

        self::assertSame(0, $report->totalRows());
        self::assertFalse($report->hasCollisions());
        self::assertFalse($report->hasConflicts());
        self::assertFalse($report->hasBlockingCheckErrors());
    }

    public function test_activity_reviewer_and_dashboard_rows_preserve_view_ready_values(): void
    {
        $occurredAt = CarbonImmutable::parse('2026-06-02 10:15:00', 'UTC');
        $activity = new WorkspaceActivityEntry(
            workspaceId: 7,
            workspaceName: 'Summer launch',
            description: 'Workspace submitted',
            event: 'submitted',
            causerId: 5,
            causerType: 'user',
            occurredAt: $occurredAt,
        );
        $requiredReviewer = new RequiredReviewer(
            requiredFor: 'page:landing',
            role: 'workspace_reviewer',
            reviewerType: 'user',
            reviewerId: 5,
        );
        $merge = new WorkspaceMergeData(
            workspaceId: 7,
            name: 'Summer launch',
            actorName: 'Ben',
            pageCount: 4,
            durationOpenHours: 26,
            publishedAt: '2026-06-03 12:00:00',
        );
        $historyEntry = new MergeHistoryEntryData(
            workspaceId: 7,
            name: 'Summer launch',
            actorName: 'Ben',
            pageCount: 4,
            durationOpenHours: 26,
            publishedAt: '2026-06-03 12:00:00',
        );

        self::assertSame(7, $activity->workspaceId);
        self::assertSame('Summer launch', $activity->workspaceName);
        self::assertSame('Workspace submitted', $activity->description);
        self::assertSame('submitted', $activity->event);
        self::assertSame(5, $activity->causerId);
        self::assertSame('user', $activity->causerType);
        self::assertSame($occurredAt, $activity->occurredAt);
        self::assertSame('page:landing', $requiredReviewer->requiredFor);
        self::assertSame('workspace_reviewer', $requiredReviewer->role);
        self::assertSame('user', $requiredReviewer->reviewerType);
        self::assertSame(5, $requiredReviewer->reviewerId);
        self::assertSame($merge->workspaceId, $historyEntry->workspaceId);
        self::assertSame($merge->name, $historyEntry->name);
        self::assertSame($merge->actorName, $historyEntry->actorName);
        self::assertSame($merge->pageCount, $historyEntry->pageCount);
        self::assertSame($merge->durationOpenHours, $historyEntry->durationOpenHours);
        self::assertSame($merge->publishedAt, $historyEntry->publishedAt);
    }
}
