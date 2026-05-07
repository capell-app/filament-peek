<?php

declare(strict_types=1);

namespace Capell\Newsletter\Contracts;

use Capell\Newsletter\Data\ProviderAudienceData;
use Capell\Newsletter\Data\ProviderSubscriberData;
use Capell\Newsletter\Data\ProviderSyncResultData;
use Capell\Newsletter\Data\ProviderWebhookEventData;
use Capell\Newsletter\Models\ProviderAudience;
use Capell\Newsletter\Models\ProviderConnection;
use Illuminate\Http\Request;

interface NewsletterProviderAdapter
{
    public function supportsOAuth(): bool;

    public function supportsProviderOwnedConfirmation(): bool;

    /**
     * @return array<int, ProviderAudienceData>
     */
    public function listAudiences(ProviderConnection $connection): array;

    public function syncSubscriber(
        ProviderConnection $connection,
        ProviderAudience $audience,
        ProviderSubscriberData $subscriber,
    ): ProviderSyncResultData;

    public function verifyWebhook(ProviderConnection $connection, Request $request): bool;

    public function normalizeWebhook(ProviderConnection $connection, Request $request): ?ProviderWebhookEventData;
}
