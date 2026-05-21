<?php

declare(strict_types=1);

use Capell\FormBuilder\Data\SubmissionPayloadData;
use Capell\FormBuilder\Events\FormSubmitted;
use Capell\FormBuilder\Models\Form;
use Capell\FormBuilder\Models\Submission;
use Capell\Newsletter\Actions\QueueProviderSyncAction;
use Capell\Newsletter\Actions\RequeueDueProviderSyncAttemptsAction;
use Capell\Newsletter\Actions\SubscribeFromFormSubmissionAction;
use Capell\Newsletter\Data\ProviderSubscriberData;
use Capell\Newsletter\Enums\AuthType;
use Capell\Newsletter\Enums\ProviderType;
use Capell\Newsletter\Enums\ResubscribePolicy;
use Capell\Newsletter\Enums\SubscriberStatus;
use Capell\Newsletter\Enums\SyncStatus;
use Capell\Newsletter\Models\FormMapping;
use Capell\Newsletter\Models\ProviderAudience;
use Capell\Newsletter\Models\ProviderConnection;
use Capell\Newsletter\Models\Subscriber;
use Capell\Newsletter\Models\SyncAttempt;
use Capell\Newsletter\Notifications\ConfirmNewsletterSubscriptionNotification;
use Capell\Newsletter\Support\NewsletterSettingsResolver;
use Capell\Newsletter\Support\Providers\FakeProviderAdapter;
use Capell\Tags\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

it('skips provider sync queueing when site connections are disabled', function (): void {
    $site = $this->createNewsletterSite();
    $subscriber = Subscriber::factory()->create(['site_id' => $site->getKey()]);
    $disabledConnection = createNewsletterProviderConnectionForCoverage((int) $site->getKey(), false, 'Disabled');

    createNewsletterProviderAudienceForCoverage($disabledConnection, 'disabled-audience');

    (new QueueProviderSyncAction)->handle($subscriber, 'unsubscribe_subscriber');

    expect(SyncAttempt::query()->count())->toBe(0);
});

it('requeues all due provider sync attempts when no positive limit is supplied', function (): void {
    $site = $this->createNewsletterSite();
    $subscriber = Subscriber::factory()->create(['site_id' => $site->getKey()]);
    $connection = createNewsletterProviderConnectionForCoverage((int) $site->getKey(), true, 'Retry');
    $firstDueAttempt = createNewsletterSyncAttemptForCoverage($subscriber, $connection, 'first', now()->subMinutes(10));
    $secondDueAttempt = createNewsletterSyncAttemptForCoverage($subscriber, $connection, 'second', now()->subMinutes(5));
    $futureAttempt = createNewsletterSyncAttemptForCoverage($subscriber, $connection, 'future', now()->addMinutes(5));

    expect((new RequeueDueProviderSyncAttemptsAction)->handle(limit: 0, dispatchJobs: false))->toBe(2)
        ->and($firstDueAttempt->refresh()->sync_status)->toBe(SyncStatus::Pending)
        ->and($firstDueAttempt->next_retry_at)->toBeNull()
        ->and($secondDueAttempt->refresh()->sync_status)->toBe(SyncStatus::Pending)
        ->and($futureAttempt->refresh()->sync_status)->toBe(SyncStatus::RetryScheduled)
        ->and($firstDueAttempt->subscriber()->getRelated())->toBeInstanceOf(Subscriber::class)
        ->and($firstDueAttempt->providerConnection()->getRelated())->toBeInstanceOf(ProviderConnection::class)
        ->and($firstDueAttempt->providerAudience()->getRelated())->toBeInstanceOf(ProviderAudience::class);
});

it('reports zero requeued sync attempts from the retry command', function (): void {
    expect(Artisan::call('newsletter:sync-retry-due', ['--limit' => 'not-a-number']))->toBe(0)
        ->and(Artisan::output())->toContain('Requeued 0 newsletter provider sync attempts.');
});

it('ignores unmapped and malformed form submission events', function (): void {
    $site = $this->createNewsletterSite();
    $form = Form::query()->create([
        'site_id' => $site->getKey(),
        'name' => 'Unmapped',
        'handle' => 'unmapped',
        'schema' => [],
        'settings' => [],
        'is_active' => true,
    ]);
    $submission = Submission::query()->create([
        'form_id' => $form->getKey(),
        'site_id' => $site->getKey(),
        'payload' => new SubmissionPayloadData(['email' => 'ignored@example.test']),
        'status' => 'new',
        'submitted_at' => now(),
    ]);
    $action = new SubscribeFromFormSubmissionAction;

    expect($action->handle(new stdClass))->toBeNull()
        ->and($action->handle(new FormSubmitted($form, $submission)))->toBeNull()
        ->and(Subscriber::query()->count())->toBe(0);
});

it('subscribes mapped submissions with consent and resolves tag mappings', function (): void {
    $site = $this->createNewsletterSite();
    $fixedTag = Tag::query()->create([
        'name' => ['en' => 'Fixed'],
        'slug' => ['en' => 'fixed'],
        'type' => 'newsletter',
    ]);
    $mappedTag = Tag::query()->create([
        'name' => ['en' => 'Mapped'],
        'slug' => ['en' => 'mapped'],
        'type' => 'newsletter',
    ]);
    $form = Form::query()->create([
        'site_id' => $site->getKey(),
        'name' => 'Mapped consent',
        'handle' => 'mapped-consent',
        'schema' => [],
        'settings' => [],
        'is_active' => true,
    ]);
    FormMapping::query()->create([
        'site_id' => $site->getKey(),
        'form_handle' => 'mapped-consent',
        'name' => 'Mapped consent',
        'email_field' => 'email',
        'consent_field' => 'consent',
        'fixed_tag_ids' => [$fixedTag->getKey(), 0],
        'field_tag_mappings' => [
            0 => ['ignored' => $fixedTag->getKey()],
            'bad' => 'not-an-array',
            'interests' => [
                'true' => $mappedTag->getKey(),
            ],
        ],
        'requires_double_opt_in' => false,
        'is_active' => true,
    ]);
    $submission = Submission::query()->create([
        'form_id' => $form->getKey(),
        'site_id' => $site->getKey(),
        'payload' => new SubmissionPayloadData([
            'email' => ' subscribed@example.test ',
            'consent' => 'yes',
            'interests' => [true],
        ]),
        'status' => 'new',
        'submitted_at' => now(),
    ]);

    $subscriber = (new SubscribeFromFormSubmissionAction)->handle(new FormSubmitted($form, $submission));

    expect($subscriber)->toBeInstanceOf(Subscriber::class)
        ->and($subscriber->status)->toBe(SubscriberStatus::Subscribed)
        ->and($subscriber->email_hash)->toBe(hash('sha256', 'subscribed@example.test'))
        ->and($subscriber->tags()->pluck('tags.id')->all())->toContain(
            (int) $fixedTag->getKey(),
            (int) $mappedTag->getKey(),
        );
});

it('covers the fake provider adapter and settings fallback policy', function (): void {
    config(['capell-newsletter.resubscribe_policy' => ResubscribePolicy::AllowWithConsent->value]);

    $site = $this->createNewsletterSite();
    $connection = createNewsletterProviderConnectionForCoverage((int) $site->getKey(), true, 'Fake Adapter');
    $audience = createNewsletterProviderAudienceForCoverage($connection, 'fake-audience');
    $adapter = new FakeProviderAdapter;
    $subscriberData = new ProviderSubscriberData(
        email: 'fake@example.test',
        status: SubscriberStatus::Subscribed,
        firstName: 'Fake',
        lastName: 'Reader',
    );

    $syncResult = $adapter->syncSubscriber($connection, $audience, $subscriberData);
    $webhook = $adapter->normalizeWebhook($connection, Request::create('/newsletter/webhook', Symfony\Component\HttpFoundation\Request::METHOD_POST, [
        'email' => 'fake@example.test',
        'status' => SubscriberStatus::Subscribed->value,
        'event_type' => 'subscribed',
        'remote_id' => 'remote-1',
    ]));

    expect($adapter->supportsOAuth())->toBeFalse()
        ->and($adapter->supportsProviderOwnedConfirmation())->toBeTrue()
        ->and($adapter->listAudiences($connection))->toHaveCount(1)
        ->and($syncResult->successful)->toBeTrue()
        ->and($syncResult->remoteStatus)->toBe(SubscriberStatus::Subscribed->value)
        ->and($adapter->verifyWebhook($connection, Request::create('/newsletter/webhook')))->toBeTrue()
        ->and($webhook?->email)->toBe('fake@example.test')
        ->and((new NewsletterSettingsResolver)->resubscribePolicyForSite((int) $site->getKey()))
        ->toBe(ResubscribePolicy::RequireDoubleOptIn);
});

it('builds confirmation notification mail content', function (): void {
    $notification = new ConfirmNewsletterSubscriptionNotification('confirm-token');
    $mail = $notification->toMail(new stdClass);

    expect($notification->via(new stdClass))->toBe(['mail'])
        ->and($mail->subject)->toBe('Confirm your newsletter subscription')
        ->and($mail->actionText)->toBe('Confirm subscription')
        ->and($mail->actionUrl)->toContain('confirm-token');
});

function createNewsletterProviderConnectionForCoverage(int $siteId, bool $isEnabled, string $name): ProviderConnection
{
    return ProviderConnection::query()->create([
        'site_id' => $siteId,
        'name' => $name,
        'provider' => ProviderType::Fake,
        'auth_type' => AuthType::ApiKey,
        'credentials' => ['api_key' => 'fake'],
        'is_enabled' => $isEnabled,
    ]);
}

function createNewsletterProviderAudienceForCoverage(ProviderConnection $connection, string $remoteId): ProviderAudience
{
    return ProviderAudience::query()->create([
        'provider_connection_id' => $connection->getKey(),
        'name' => ucfirst(str_replace('-', ' ', $remoteId)),
        'remote_id' => $remoteId,
        'is_default' => true,
        'sync_subscribed_only' => true,
    ]);
}

function createNewsletterSyncAttemptForCoverage(
    Subscriber $subscriber,
    ProviderConnection $connection,
    string $payloadHash,
    DateTimeInterface $nextRetryAt,
): SyncAttempt {
    return SyncAttempt::query()->create([
        'subscriber_id' => $subscriber->getKey(),
        'provider_connection_id' => $connection->getKey(),
        'operation' => 'sync_subscriber',
        'sync_status' => SyncStatus::RetryScheduled,
        'payload_hash' => $payloadHash,
        'attempts' => 1,
        'last_attempted_at' => now()->subHour(),
        'next_retry_at' => $nextRetryAt,
    ]);
}
