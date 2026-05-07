<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\Subscribers\Pages;

use Capell\Newsletter\Actions\QueueProviderSyncAction;
use Capell\Newsletter\Actions\UpsertSubscriberAction;
use Capell\Newsletter\Data\ConsentEvidenceData;
use Capell\Newsletter\Data\SubscriberData;
use Capell\Newsletter\Enums\ConsentEventType;
use Capell\Newsletter\Enums\SubscriberStatus;
use Capell\Newsletter\Filament\Resources\Subscribers\SubscriberResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSubscriber extends EditRecord
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $subscriber = UpsertSubscriberAction::run($this->subscriberData($data), $this->adminEvidence(), ConsentEventType::AdminUpdated);

        QueueProviderSyncAction::run($subscriber);

        return $subscriber;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function subscriberData(array $data): SubscriberData
    {
        return new SubscriberData(
            siteId: (int) $data['site_id'],
            email: (string) $data['email'],
            status: SubscriberStatus::from((string) $data['status']),
            firstName: is_string($data['first_name'] ?? null) ? $data['first_name'] : null,
            lastName: is_string($data['last_name'] ?? null) ? $data['last_name'] : null,
        );
    }

    private function adminEvidence(): ConsentEvidenceData
    {
        $actor = auth()->user();

        return new ConsentEvidenceData(
            sourceType: 'admin',
            sourceId: $actor instanceof Model ? (string) $actor->getKey() : null,
        );
    }
}
