<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Data;

use Capell\PublishingStudio\Enums\SchedulerEventStateEnum;
use Capell\PublishingStudio\Enums\SchedulerEventTypeEnum;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;

final class SchedulerEventData extends Data
{
    public function __construct(
        public string $id,
        public string $sourceType,
        public int $sourceId,
        public string $title,
        public SchedulerEventTypeEnum $eventType,
        public CarbonInterface $scheduledFor,
        public string $status,
        public ?string $description = null,
        public ?string $recordUrl = null,
        public ?SchedulerEventStateEnum $state = null,
        public ?int $siteId = null,
        public ?string $siteName = null,
        public ?int $ownerId = null,
        public ?string $ownerName = null,
        public ?string $timezone = null,
        public ?string $failure = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toTableRecord(): array
    {
        return [
            'id' => $this->id,
            'source_type' => $this->sourceType,
            'source_id' => $this->sourceId,
            'title' => $this->title,
            'event_type' => $this->eventType->value,
            'event_type_label' => $this->eventType->getLabel(),
            'event_type_color' => $this->eventType->getColor(),
            'scheduled_for' => $this->scheduledFor,
            'status' => $this->status,
            'description' => $this->description,
            'record_url' => $this->recordUrl,
            'state' => $this->state?->value,
            'state_label' => $this->state?->getLabel(),
            'state_color' => $this->state?->getColor(),
            'site_id' => $this->siteId,
            'site_name' => $this->siteName,
            'owner_id' => $this->ownerId,
            'owner_name' => $this->ownerName,
            'timezone' => $this->timezone,
            'failure' => $this->failure,
        ];
    }
}
