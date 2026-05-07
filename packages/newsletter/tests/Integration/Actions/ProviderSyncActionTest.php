<?php

declare(strict_types=1);

use Capell\Newsletter\Actions\SyncSubscriberToProviderAction;
use Capell\Newsletter\Enums\AuthType;
use Capell\Newsletter\Enums\ProviderType;
use Capell\Newsletter\Enums\SubscriberStatus;
use Capell\Newsletter\Enums\SyncStatus;
use Capell\Newsletter\Models\ProviderAudience;
use Capell\Newsletter\Models\ProviderConnection;
use Capell\Newsletter\Models\ProviderSubscriber;
use Capell\Newsletter\Models\Subscriber;
use Capell\Newsletter\Models\SyncAttempt;

it('syncs durable attempts through a provider adapter', function (): void {
    $site = $this->createNewsletterSite();
    $subscriber = Subscriber::factory()->create([
        'site_id' => $site->getKey(),
        'email' => 'sync@example.com',
    ]);
    $connection = ProviderConnection::query()->create([
        'site_id' => $site->getKey(),
        'name' => 'Fake',
        'provider' => ProviderType::Fake,
        'auth_type' => AuthType::ApiKey,
        'credentials' => ['api_key' => 'fake'],
        'is_enabled' => true,
    ]);
    $audience = ProviderAudience::query()->create([
        'provider_connection_id' => $connection->getKey(),
        'name' => 'Default',
        'remote_id' => 'fake-audience',
        'is_default' => true,
        'sync_subscribed_only' => true,
    ]);

    $syncAttempt = SyncAttempt::query()->create([
        'subscriber_id' => $subscriber->getKey(),
        'provider_connection_id' => $connection->getKey(),
        'provider_audience_id' => $audience->getKey(),
        'operation' => 'sync_subscriber',
        'sync_status' => SyncStatus::Pending,
        'attempts' => 0,
    ]);

    SyncSubscriberToProviderAction::run($syncAttempt);

    expect($syncAttempt->refresh()->sync_status)->toBe(SyncStatus::Succeeded)
        ->and(ProviderSubscriber::query()->where('subscriber_id', $subscriber->getKey())->exists())->toBeTrue();
});

it('normalizes provider webhooks into local subscriber state', function (): void {
    $site = $this->createNewsletterSite();
    $connection = ProviderConnection::query()->create([
        'site_id' => $site->getKey(),
        'name' => 'Fake',
        'provider' => ProviderType::Fake,
        'auth_type' => AuthType::ApiKey,
        'credentials' => ['api_key' => 'fake'],
        'is_enabled' => true,
    ]);

    $this->postJson(route('capell-newsletter.provider-webhook', ['providerConnection' => $connection]), [
        'email' => 'webhook@example.com',
        'status' => SubscriberStatus::Unsubscribed->value,
        'event_type' => 'unsubscribe',
    ])->assertOk();

    expect(Subscriber::query()->forEmail($site->getKey(), 'webhook@example.com')->first()?->status)
        ->toBe(SubscriberStatus::Unsubscribed);
});
