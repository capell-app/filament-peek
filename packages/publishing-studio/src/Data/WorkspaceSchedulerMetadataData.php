<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

final class WorkspaceSchedulerMetadataData extends Data
{
    public function __construct(
        public ?CarbonImmutable $publishAt,
        public ?CarbonImmutable $unpublishAt,
        public ?CarbonImmutable $embargoUntil,
        public ?CarbonImmutable $reviewReminderAt,
        public string $displayTimezone = 'UTC',
    ) {}
}
