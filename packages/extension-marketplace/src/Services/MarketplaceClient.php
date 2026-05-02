<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Services;

use Capell\ExtensionMarketplace\Data\ExtensionListingData;
use Capell\ExtensionMarketplace\Data\HeartbeatResultData;
use Capell\ExtensionMarketplace\Data\MarketplaceInstallAuthorizationData;
use Capell\ExtensionMarketplace\Data\MarketplaceUpgradeAuthorizationData;
use Capell\ExtensionMarketplace\Exceptions\PurchaseRequiredException;
use Capell\ExtensionMarketplace\Models\MarketplaceInstance;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

final class MarketplaceClient
{
    public const INSTANCE_NOT_REGISTERED_MESSAGE = 'Marketplace instance is not registered. Connect and verify this domain before requesting authorization.';

    public const DEFAULT_EXTENSION_SORT = 'featured_latest';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function heartbeat(array $payload): HeartbeatResultData
    {
        $heartbeatUrl = config('capell-extension-marketplace.marketplace.base_url') . '/instances/heartbeat';

        $response = Http::timeout(config('capell-extension-marketplace.marketplace.timeout_seconds', 10))
            ->acceptJson()
            ->post($heartbeatUrl, $payload);

        if (! $response->successful()) {
            throw new RuntimeException(
                'The marketplace rejected the heartbeat with HTTP status ' . $response->status() . '.'
                . $this->responseFailureDetails($response, $heartbeatUrl)
                . ' Check the marketplace URL, instance ID, and server logs.',
            );
        }

        $responseData = $response->json('data');

        if (! is_array($responseData)) {
            throw new RuntimeException(
                'The marketplace response did not include the expected data payload.'
                . $this->responseFailureDetails($response, $heartbeatUrl),
            );
        }

        $this->validateHeartbeatResponseData($responseData, $response, $heartbeatUrl);

        return HeartbeatResultData::fromApiResponse($responseData);
    }

    /** @return array<int, ExtensionListingData> */
    public function listExtensions(
        string $search = '',
        string $kind = '',
        bool $freeOnly = false,
        string $sort = self::DEFAULT_EXTENSION_SORT,
        ?int $priceMinCents = null,
        ?int $priceMaxCents = null,
        ?string $capellVersion = null,
        ?string $laravelVersion = null,
        ?string $livewireVersion = null,
        ?string $filamentVersion = null,
        ?string $category = null,
        array $capabilities = [],
    ): array {
        $cachePayload = [
            'search' => $search,
            'kind' => $kind,
            'free' => $freeOnly,
            'sort' => $sort,
            'price_min_cents' => $priceMinCents,
            'price_max_cents' => $priceMaxCents,
            'capell_version' => $capellVersion,
            'laravel_version' => $laravelVersion,
            'livewire_version' => $livewireVersion,
            'filament_version' => $filamentVersion,
            'category' => $category,
            'capabilities' => $capabilities,
        ];
        $cacheKey = 'capell-extension-marketplace.marketplace.extensions.' . hash('xxh3', json_encode($cachePayload, JSON_THROW_ON_ERROR));
        $ttl = config('capell-extension-marketplace.marketplace.cache_ttl_seconds', 300);

        $items = Cache::remember($cacheKey, $ttl, function () use ($search, $kind, $freeOnly, $sort, $priceMinCents, $priceMaxCents, $capellVersion, $laravelVersion, $livewireVersion, $filamentVersion, $category, $capabilities): array {
            $params = array_filter(
                [
                    'search' => $search,
                    'kind' => $kind,
                    'free' => $freeOnly ? '1' : '',
                    'sort' => $sort,
                    'min_price_cents' => $priceMinCents === null ? '' : (string) $priceMinCents,
                    'max_price_cents' => $priceMaxCents === null ? '' : (string) $priceMaxCents,
                    'capell_version' => $capellVersion ?? '',
                    'laravel_version' => $laravelVersion ?? '',
                    'livewire_version' => $livewireVersion ?? '',
                    'filament_version' => $filamentVersion ?? '',
                    'category' => $category ?? '',
                    'capabilities' => implode(',', $capabilities),
                ],
                fn (string $value): bool => $value !== '',
            );
            $url = config('capell-extension-marketplace.marketplace.base_url') . '/extensions';
            $items = [];
            $visitedUrls = [];

            do {
                $visitedUrls[] = $url;
                $pendingRequest = Http::timeout(config('capell-extension-marketplace.marketplace.timeout_seconds', 10));
                $response = $params === []
                    ? $pendingRequest->get($url)
                    : $pendingRequest->get($url, $params);

                $items = [
                    ...$items,
                    ...($response->json('data') ?? []),
                ];

                $url = $response->json('links.next');
                $params = [];
            } while (is_string($url) && $url !== '' && ! in_array($url, $visitedUrls, true));

            return $items;
        });

        return array_map(
            ExtensionListingData::fromApiResponse(...),
            $items,
        );
    }

    public function getExtension(string $slug): ?ExtensionListingData
    {
        $cacheKey = 'capell-extension-marketplace.marketplace.extension.' . $slug;
        $ttl = config('capell-extension-marketplace.marketplace.cache_ttl_seconds', 300);

        $item = Cache::remember($cacheKey, $ttl, function () use ($slug): ?array {
            $response = Http::timeout(config('capell-extension-marketplace.marketplace.timeout_seconds', 10))
                ->get(config('capell-extension-marketplace.marketplace.base_url') . '/extensions/' . $slug);

            if ($response->notFound()) {
                return null;
            }

            return $response->json('data');
        });

        return $item !== null ? ExtensionListingData::fromApiResponse($item) : null;
    }

    public function createInstallAuthorization(
        string $slug,
        ?string $licenseKey,
        ?string $email,
        string $domain,
        array $installOptions = [],
    ): MarketplaceInstallAuthorizationData {
        $response = $this->postSignedJson('/extensions/' . $slug . '/install-authorization', [
            'license_key' => $licenseKey,
            'email' => $email,
            'domain' => $domain,
            'install_options' => $installOptions,
        ]);

        $this->throwIfPurchaseRequired($response);
        $response->throw();

        return MarketplaceInstallAuthorizationData::fromApiResponse($response->json() ?? []);
    }

    public function createUpgradeAuthorization(
        string $composerName,
        string $currentVersion,
        string $domain,
    ): MarketplaceUpgradeAuthorizationData {
        $response = $this->postSignedJson('/extensions/upgrade-authorization', [
            'composer_name' => $composerName,
            'current_version' => $currentVersion,
            'domain' => $domain,
        ])
            ->throw();

        return MarketplaceUpgradeAuthorizationData::fromApiResponse($response->json() ?? []);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function postSignedJson(string $path, array $payload): Response
    {
        $jsonPayload = json_encode($payload, JSON_THROW_ON_ERROR);
        try {
            $marketplaceInstance = MarketplaceInstance::query()
                ->latest('last_heartbeat_at')
                ->first();
        } catch (Throwable) {
            $marketplaceInstance = null;
        }

        $signingSecret = $marketplaceInstance?->signing_secret_encrypted;
        $instanceId = $marketplaceInstance?->instance_id;

        if (! is_string($instanceId) || $instanceId === '' || ! is_string($signingSecret) || $signingSecret === '') {
            $instanceId = config('capell-extension-marketplace.instance.id');
            $signingSecret = config('capell-extension-marketplace.marketplace.webhook_secret');
        }

        throw_if(! is_string($instanceId) || $instanceId === '' || ! is_string($signingSecret) || $signingSecret === '', RuntimeException::class, self::INSTANCE_NOT_REGISTERED_MESSAGE);

        return Http::timeout(config('capell-extension-marketplace.marketplace.timeout_seconds', 10))
            ->withHeaders([
                'X-Capell-Instance' => $instanceId,
                'X-Capell-Signature' => 'sha256=' . hash_hmac('sha256', $jsonPayload, $signingSecret),
            ])
            ->withBody($jsonPayload, 'application/json')
            ->post(config('capell-extension-marketplace.marketplace.base_url') . $path);
    }

    private function throwIfPurchaseRequired(Response $response): void
    {
        if (! in_array($response->status(), [402, 403, 422], true)) {
            return;
        }

        $purchaseUrl = $response->json('data.purchase_url')
            ?? $response->json('data.checkout_url')
            ?? $response->json('purchase_url')
            ?? $response->json('checkout_url');

        if (! is_string($purchaseUrl) || $purchaseUrl === '') {
            return;
        }

        $message = $response->json('message');

        throw new PurchaseRequiredException(
            purchaseUrl: $purchaseUrl,
            message: is_string($message) && $message !== '' ? $message : 'Purchase is required before this plugin can be installed.',
        );
    }

    /**
     * @param  array<string, mixed>  $responseData
     */
    private function validateHeartbeatResponseData(array $responseData, Response $response, string $heartbeatUrl): void
    {
        $instanceId = $responseData['instance_id'] ?? null;
        $signingSecret = $responseData['signing_secret'] ?? null;
        $updates = $responseData['updates'] ?? null;
        $advisories = $responseData['advisories'] ?? null;

        if (! is_string($instanceId) || $instanceId === '') {
            throw new RuntimeException(
                'The marketplace response did not include an instance ID.'
                . $this->responseFailureDetails($response, $heartbeatUrl),
            );
        }

        if ($signingSecret !== null && (! is_string($signingSecret) || $signingSecret === '')) {
            throw new RuntimeException(
                'The marketplace response included an invalid signing secret.'
                . $this->responseFailureDetails($response, $heartbeatUrl),
            );
        }

        if (($updates !== null && ! is_array($updates)) || ($advisories !== null && ! is_array($advisories))) {
            throw new RuntimeException(
                'The marketplace response did not include update and advisory lists.'
                . $this->responseFailureDetails($response, $heartbeatUrl),
            );
        }

        foreach (($updates ?? []) as $update) {
            if (! is_array($update)) {
                throw new RuntimeException(
                    'The marketplace response included an invalid update notice.'
                    . $this->responseFailureDetails($response, $heartbeatUrl),
                );
            }
        }

        foreach (($advisories ?? []) as $advisory) {
            if (! is_array($advisory)) {
                throw new RuntimeException(
                    'The marketplace response included an invalid advisory notice.'
                    . $this->responseFailureDetails($response, $heartbeatUrl),
                );
            }
        }
    }

    private function responseFailureDetails(Response $response, string $heartbeatUrl): string
    {
        $contentType = $response->header('content-type');

        $details = ' Heartbeat URL: ' . $heartbeatUrl . '.';

        if ($contentType !== '') {
            $details .= ' The marketplace returned ' . $contentType . '.';
        }

        $detail = $response->json('error')
            ?? $response->json('message')
            ?? $this->htmlResponseSummary($response)
            ?? $response->body();

        if (! is_scalar($detail)) {
            return $details;
        }

        $detail = trim((string) $detail);

        if ($detail === '') {
            return $details;
        }

        return $details . ' Marketplace response: ' . str($detail)->limit(300);
    }

    private function htmlResponseSummary(Response $response): ?string
    {
        $contentType = strtolower($response->header('content-type'));
        $body = $response->body();

        if (! str_contains($contentType, 'html') && ! str_starts_with(ltrim($body), '<')) {
            return null;
        }

        if (preg_match('/<title[^>]*>(.*?)<\\/title>/is', $body, $matches) === 1) {
            $title = trim(html_entity_decode(strip_tags($matches[1])));

            if ($title !== '') {
                return 'The heartbeat URL returned HTML instead of JSON. Page title: ' . $title;
            }
        }

        return 'The heartbeat URL returned HTML instead of JSON.';
    }
}
