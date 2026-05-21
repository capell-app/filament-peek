<?php

declare(strict_types=1);

namespace Capell\Newsletter\Actions;

use Capell\Newsletter\Data\ConsentEvidenceData;
use Capell\Newsletter\Data\SubscriberData;
use Capell\Newsletter\Enums\ConsentEventType;
use Capell\Newsletter\Models\ProviderConnection;
use Capell\Newsletter\Models\Subscriber;
use Capell\Newsletter\Support\Providers\ProviderAdapterRegistry;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\Concerns\AsAction;

class HandleProviderWebhookAction
{
    use AsAction;

    public function handle(ProviderConnection $connection, Request $request): ?Subscriber
    {
        $adapter = resolve(ProviderAdapterRegistry::class)->resolve($connection->provider);

        throw_unless($adapter->verifyWebhook($connection, $request), AuthorizationException::class, 'Invalid newsletter provider webhook signature.');

        $event = $adapter->normalizeWebhook($connection, $request);

        if ($event === null) {
            return null;
        }

        $remoteEventId = $this->remoteEventId($connection, $event->remoteId, $event->eventType, $event->email, $request);

        if ($this->alreadyProcessed($connection, $remoteEventId, $event->eventType)) {
            return Subscriber::query()
                ->forEmail($connection->site_id, $event->email)
                ->first();
        }

        $subscriber = UpsertSubscriberAction::run(new SubscriberData(
            siteId: $connection->site_id,
            email: $event->email,
            status: $event->status,
        ), new ConsentEvidenceData(
            sourceType: 'provider_webhook',
            sourceId: $event->remoteId,
            extra: ['event_type' => $event->eventType],
        ), ConsentEventType::ProviderWebhook, false);

        RecordConsentEventAction::run(
            $subscriber,
            ConsentEventType::ProviderWebhook,
            new ConsentEvidenceData(
                sourceType: 'provider_webhook',
                sourceId: $event->remoteId,
                extra: ['event_type' => $event->eventType],
            ),
            $event->status,
            $connection,
            $event->payload,
        );

        $this->markProcessed($connection, $remoteEventId, $event->eventType);

        return $subscriber;
    }

    private function alreadyProcessed(ProviderConnection $connection, string $remoteId, string $eventType): bool
    {
        if (! Schema::hasTable('newsletter_processed_webhook_events')) {
            return false;
        }

        return DB::table('newsletter_processed_webhook_events')
            ->where('provider_connection_id', $connection->id)
            ->where('remote_event_id', $remoteId)
            ->where('event_type', $eventType)
            ->exists();
    }

    private function markProcessed(ProviderConnection $connection, string $remoteId, string $eventType): void
    {
        if (! Schema::hasTable('newsletter_processed_webhook_events')) {
            return;
        }

        $now = now();

        DB::table('newsletter_processed_webhook_events')->insertOrIgnore([
            'provider_connection_id' => $connection->id,
            'remote_event_id' => $remoteId,
            'event_type' => $eventType,
            'processed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function remoteEventId(
        ProviderConnection $connection,
        ?string $remoteId,
        string $eventType,
        string $email,
        Request $request,
    ): string {
        if (is_string($remoteId) && $remoteId !== '') {
            return $remoteId;
        }

        return hash('sha256', implode('|', [
            $connection->provider->value,
            $eventType,
            Subscriber::emailHash($email),
            hash('sha256', $request->getContent()),
        ]));
    }
}
