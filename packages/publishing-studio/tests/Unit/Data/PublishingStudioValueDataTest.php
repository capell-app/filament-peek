<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Tests\Unit\Data;

use Capell\PublishingStudio\Checks\PublishCheckResult;
use Capell\PublishingStudio\Checks\PublishCheckSeverity;
use Capell\PublishingStudio\CloneOptions;
use Capell\PublishingStudio\Data\Imports\PageImportStatusData;
use Capell\PublishingStudio\Data\Imports\PageImportWizardStateData;
use Capell\PublishingStudio\Data\SchedulerEventData;
use Capell\PublishingStudio\Data\WorkspaceSettingsData;
use Capell\PublishingStudio\Enums\SchedulerEventStateEnum;
use Capell\PublishingStudio\Enums\SchedulerEventTypeEnum;
use Capell\PublishingStudio\Rollback\EntityRollbackReport;
use Capell\PublishingStudio\Services\MediaDiffResult;
use Carbon\CarbonImmutable;
use Illuminate\Container\Container;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PublishingStudioValueDataTest extends TestCase
{
    /**
     * @return Iterator<string, array{(string | null), (string | null), bool}>
     */
    public static function mediaDiffProvider(): Iterator
    {
        yield 'no media' => [null, null, false];
        yield 'before only' => ['https://example.test/before.jpg', null, true];
        yield 'after only' => [null, 'https://example.test/after.jpg', true];
        yield 'both sides' => ['https://example.test/before.jpg', 'https://example.test/after.jpg', true];
    }

    public function test_clone_options_default_to_copying_draft_content_and_settings(): void
    {
        $options = new CloneOptions;

        $this->assertTrue($options->copyDrafts);
        $this->assertTrue($options->copySettings);
        $this->assertNull($options->newName);
        $this->assertNull($options->newSlug);
        $this->assertNull($options->description);
    }

    public function test_workspace_settings_default_to_two_approval_levels(): void
    {
        $this->assertSame(2, (new WorkspaceSettingsData)->requiredApprovalLevels);
    }

    public function test_page_import_status_exposes_stable_notice_constants_and_payload_state(): void
    {
        $status = new PageImportStatusData(
            step: 'validate',
            sessionStatus: 'Failed',
            resultSummary: ['blocking_errors' => 2],
            failureReason: 'Checksum mismatch',
            targetWorkspaceId: 42,
            notice: PageImportStatusData::NOTICE_SUMMARY_BLOCKING_ERRORS,
            noticeBody: 'Fix validation errors before dispatching.',
        );

        $this->assertSame('confirmation_mismatch', PageImportStatusData::NOTICE_CONFIRMATION_MISMATCH);
        $this->assertSame('import_queued', PageImportStatusData::NOTICE_IMPORT_QUEUED);
        $this->assertSame('validate', $status->step);
        $this->assertSame('Failed', $status->sessionStatus);
        $this->assertSame(['blocking_errors' => 2], $status->resultSummary);
        $this->assertSame('Checksum mismatch', $status->failureReason);
        $this->assertSame(42, $status->targetWorkspaceId);
        $this->assertSame('summary_blocking_errors', $status->notice);
        $this->assertSame('Fix validation errors before dispatching.', $status->noticeBody);
    }

    public function test_page_import_wizard_state_carries_review_and_validation_state(): void
    {
        $state = new PageImportWizardStateData(
            step: 'resolve',
            sessionId: 7,
            reviewRows: [['title' => 'Imported page']],
            pageDecisions: ['page-1' => ['action' => 'create']],
            resolveRows: [['key' => 'author:ben']],
            relationDecisions: ['author:ben' => ['action' => 'use_existing', 'target_id' => 9]],
            validationSummary: ['warnings' => 1],
            confirmationExpected: 'IMPORT 1 PAGE',
            notice: PageImportWizardStateData::NOTICE_UNRESOLVED_REFERENCES,
            noticeCount: 1,
        );

        $this->assertSame('blocked_by_workspace_conflict', PageImportWizardStateData::NOTICE_BLOCKED_BY_WORKSPACE_CONFLICT);
        $this->assertSame('blocked_pending_decisions', PageImportWizardStateData::NOTICE_BLOCKED_PENDING_DECISIONS);
        $this->assertSame('resolve', $state->step);
        $this->assertSame(7, $state->sessionId);
        $this->assertCount(1, $state->reviewRows);
        $this->assertSame('create', $state->pageDecisions['page-1']['action']);
        $this->assertCount(1, $state->resolveRows);
        $this->assertSame(9, $state->relationDecisions['author:ben']['target_id']);
        $this->assertSame(['warnings' => 1], $state->validationSummary);
        $this->assertSame('IMPORT 1 PAGE', $state->confirmationExpected);
        $this->assertSame('unresolved_references', $state->notice);
        $this->assertSame(1, $state->noticeCount);
    }

    public function test_scheduler_event_table_record_includes_labels_colours_and_source_metadata(): void
    {
        Container::getInstance()->instance('translator', new class
        {
            /**
             * @param  array<string, mixed>  $replace
             */
            public function get(string $key, array $replace = [], ?string $locale = null, bool $fallback = true): string
            {
                return $key;
            }
        });

        $scheduledFor = CarbonImmutable::parse('2026-06-01 09:30:00', 'UTC');
        $event = new SchedulerEventData(
            id: 'workspace:42:publish',
            sourceType: 'workspace',
            sourceId: 42,
            title: 'Summer launch',
            eventType: SchedulerEventTypeEnum::Publish,
            scheduledFor: $scheduledFor,
            status: 'scheduled',
            description: 'Ready to publish.',
            recordUrl: 'https://example.test/admin/workspaces/42',
            state: SchedulerEventStateEnum::Scheduled,
            siteId: 3,
            siteName: 'Main site',
            ownerId: 7,
            ownerName: 'Editor',
            timezone: 'Europe/London',
        );

        $this->assertSame([
            'id' => 'workspace:42:publish',
            'source_type' => 'workspace',
            'source_id' => 42,
            'title' => 'Summer launch',
            'event_type' => 'publish',
            'event_type_label' => 'capell-publishing-studio::scheduler.event_types.publish',
            'event_type_color' => 'success',
            'scheduled_for' => $scheduledFor,
            'status' => 'scheduled',
            'description' => 'Ready to publish.',
            'record_url' => 'https://example.test/admin/workspaces/42',
            'state' => 'scheduled',
            'state_label' => 'capell-publishing-studio::scheduler.states.scheduled',
            'state_color' => 'info',
            'site_id' => 3,
            'site_name' => 'Main site',
            'owner_id' => 7,
            'owner_name' => 'Editor',
            'timezone' => 'Europe/London',
            'failure' => null,
        ], $event->toTableRecord());
    }

    public function test_publish_check_result_reports_errors_and_clean_results_independently(): void
    {
        $cleanError = new PublishCheckResult(
            identifier: 'seo',
            label: 'SEO',
            severity: PublishCheckSeverity::Error,
        );

        $dirtyWarning = new PublishCheckResult(
            identifier: 'links',
            label: 'Links',
            severity: PublishCheckSeverity::Warn,
            messages: ['Broken link found.'],
        );

        $this->assertTrue($cleanError->isError());
        $this->assertTrue($cleanError->isClean());
        $this->assertFalse($dirtyWarning->isError());
        $this->assertFalse($dirtyWarning->isClean());
    }

    public function test_entity_rollback_report_records_preview_and_execution_outcomes(): void
    {
        $report = new EntityRollbackReport(
            modelClass: 'App\\Models\\Page',
            entityUuid: 'page-uuid',
            targetVersionId: 15,
            restoredId: 22,
            replacedId: 24,
            noOp: false,
        );

        $this->assertSame('App\\Models\\Page', $report->modelClass);
        $this->assertSame('page-uuid', $report->entityUuid);
        $this->assertSame(15, $report->targetVersionId);
        $this->assertSame(22, $report->restoredId);
        $this->assertSame(24, $report->replacedId);
        $this->assertFalse($report->noOp);
    }

    #[DataProvider('mediaDiffProvider')]
    public function test_media_diff_result_reports_visual_diffs_when_either_media_side_exists(
        ?string $beforeUrl,
        ?string $afterUrl,
        bool $expected,
    ): void {
        $result = new MediaDiffResult(
            beforeUrl: $beforeUrl,
            afterUrl: $afterUrl,
            perceptualHashDelta: null,
            contentChanged: $expected,
        );

        $this->assertSame($expected, $result->hasVisualDiff());
    }
}
