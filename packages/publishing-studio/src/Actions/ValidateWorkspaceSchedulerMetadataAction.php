<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Actions;

use Capell\PublishingStudio\Data\WorkspaceSchedulerMetadataData;
use Capell\PublishingStudio\Enums\WorkspaceStatusEnum;
use Capell\PublishingStudio\Exceptions\InvalidSchedulerMetadataException;
use Capell\PublishingStudio\Models\Workspace;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

final class ValidateWorkspaceSchedulerMetadataAction
{
    use AsAction;

    /**
     * @param  array{publish_at?: CarbonInterface|string|null, unpublish_at?: CarbonInterface|string|null, embargo_until?: CarbonInterface|string|null, review_reminder_at?: CarbonInterface|string|null, display_timezone?: string|null}  $metadata
     */
    public function handle(Workspace $workspace, array $metadata, bool $allowPastPublish = false): WorkspaceSchedulerMetadataData
    {
        $publishAt = $this->parseDate(array_key_exists('publish_at', $metadata) ? $metadata['publish_at'] : $workspace->publish_at, 'publish_at');
        $unpublishAt = $this->parseDate(array_key_exists('unpublish_at', $metadata) ? $metadata['unpublish_at'] : $workspace->unpublish_at, 'unpublish_at');
        $embargoUntil = $this->parseDate(array_key_exists('embargo_until', $metadata) ? $metadata['embargo_until'] : $workspace->embargo_until, 'embargo_until');
        $reviewReminderAt = $this->parseDate(array_key_exists('review_reminder_at', $metadata) ? $metadata['review_reminder_at'] : $workspace->review_reminder_at, 'review_reminder_at');
        $displayTimezone = $this->displayTimezone($metadata['display_timezone'] ?? null);

        if (! $allowPastPublish && $publishAt instanceof CarbonImmutable && $publishAt->lessThanOrEqualTo(CarbonImmutable::now())) {
            throw new InvalidSchedulerMetadataException((string) __('capell-publishing-studio::scheduler.validation.publish_future'));
        }

        if ($publishAt instanceof CarbonImmutable
            && $workspace->status !== WorkspaceStatusEnum::Approved
            && $workspace->status !== WorkspaceStatusEnum::Scheduled) {
            throw new InvalidSchedulerMetadataException((string) __('capell-publishing-studio::scheduler.validation.publish_status'));
        }

        if ($unpublishAt instanceof CarbonImmutable) {
            $minimumUnpublishAt = $publishAt ?? $workspace->published_at;

            if ($minimumUnpublishAt instanceof CarbonInterface && $unpublishAt->lessThanOrEqualTo($minimumUnpublishAt)) {
                throw new InvalidSchedulerMetadataException((string) __('capell-publishing-studio::scheduler.validation.unpublish_after_publish'));
            }
        }

        if ($publishAt instanceof CarbonImmutable
            && $embargoUntil instanceof CarbonImmutable
            && $embargoUntil->greaterThan($publishAt)) {
            throw new InvalidSchedulerMetadataException((string) __('capell-publishing-studio::scheduler.validation.embargo_before_publish'));
        }

        if ($publishAt instanceof CarbonImmutable
            && $reviewReminderAt instanceof CarbonImmutable
            && $reviewReminderAt->greaterThanOrEqualTo($publishAt)) {
            throw new InvalidSchedulerMetadataException((string) __('capell-publishing-studio::scheduler.validation.review_before_publish'));
        }

        if ($reviewReminderAt instanceof CarbonImmutable
            && ! in_array($workspace->status, [WorkspaceStatusEnum::InReview, WorkspaceStatusEnum::Approved, WorkspaceStatusEnum::Scheduled], true)) {
            throw new InvalidSchedulerMetadataException((string) __('capell-publishing-studio::scheduler.validation.review_status'));
        }

        return new WorkspaceSchedulerMetadataData(
            publishAt: $publishAt,
            unpublishAt: $unpublishAt,
            embargoUntil: $embargoUntil,
            reviewReminderAt: $reviewReminderAt,
            displayTimezone: $displayTimezone,
        );
    }

    private function parseDate(CarbonInterface|string|null $value, string $field): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof CarbonInterface) {
            return CarbonImmutable::instance($value)->utc();
        }

        try {
            return CarbonImmutable::parse($value)->utc();
        } catch (Throwable) {
            throw new InvalidSchedulerMetadataException((string) __('capell-publishing-studio::scheduler.validation.invalid_date', ['field' => $field]));
        }
    }

    private function displayTimezone(?string $timezone): string
    {
        if ($timezone === null || $timezone === '') {
            return config('app.timezone', 'UTC');
        }

        return $timezone;
    }
}
