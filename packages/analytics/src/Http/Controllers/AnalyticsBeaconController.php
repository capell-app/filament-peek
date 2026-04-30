<?php

declare(strict_types=1);

namespace Capell\Analytics\Http\Controllers;

use Capell\Analytics\Actions\RecordAnalyticsEventAction;
use Capell\Analytics\Actions\RecordClickAction;
use Capell\Analytics\Actions\RecordCustomActionAction;
use Capell\Analytics\Actions\RecordPageViewAction;
use Capell\Analytics\Data\AnalyticsEventData;
use Capell\Analytics\Enums\AnalyticsEventType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class AnalyticsBeaconController
{
    public function __invoke(Request $request): Response
    {
        $validated = $request->validate([
            'visit_id' => ['nullable', 'string', 'max:80'],
            'events' => ['required', 'array', 'max:25'],
            'events.*.type' => ['required', Rule::enum(AnalyticsEventType::class)],
            'events.*.url' => ['required', 'url', 'max:2048'],
            'events.*.title' => ['nullable', 'string', 'max:255'],
            'events.*.occurred_at' => ['nullable', 'date'],
            'events.*.event_name' => ['nullable', 'string', 'max:100'],
            'events.*.label' => ['nullable', 'string', 'max:255'],
            'events.*.location' => ['nullable', 'string', 'max:255'],
            'events.*.target_selector' => ['nullable', 'string', 'max:500'],
            'events.*.viewport_x' => ['nullable', 'integer'],
            'events.*.viewport_y' => ['nullable', 'integer'],
            'events.*.document_x' => ['nullable', 'integer'],
            'events.*.document_y' => ['nullable', 'integer'],
            'events.*.metadata' => ['nullable', 'array'],
        ]);

        $visitUuid = isset($validated['visit_id']) && is_string($validated['visit_id'])
            ? $validated['visit_id']
            : null;

        /** @var list<array<string, mixed>> $events */
        $events = $validated['events'];

        foreach ($events as $event) {
            $eventData = AnalyticsEventData::from($event);
            $occurredAt = isset($event['occurred_at']) && is_string($event['occurred_at'])
                ? $event['occurred_at']
                : null;

            match ($eventData->type) {
                AnalyticsEventType::PageView => RecordPageViewAction::run($visitUuid, $eventData, $occurredAt),
                AnalyticsEventType::Click => RecordClickAction::run($visitUuid, $eventData, $occurredAt),
                AnalyticsEventType::Custom => RecordCustomActionAction::run($visitUuid, $eventData, $occurredAt),
                default => RecordAnalyticsEventAction::run($visitUuid, $eventData, $occurredAt),
            };
        }

        return response()->noContent();
    }
}
