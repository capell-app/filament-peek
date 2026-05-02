<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Actions;

use Capell\ExtensionMarketplace\Enums\MarketplaceRegistrationStatus;
use Capell\ExtensionMarketplace\Models\MarketplaceRegistrationSession;
use Capell\ExtensionMarketplace\Support\MarketplaceBaseUrl;
use Capell\ExtensionMarketplace\Support\MarketplaceWebhookUrl;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;
use Throwable;

final class StartMarketplaceRegistrationAction
{
    use AsAction;

    public function handle(): MarketplaceRegistrationSession
    {
        $appUrl = rtrim((string) config('app.url'), '/');
        $host = parse_url($appUrl, PHP_URL_HOST);

        throw_if(! is_string($host) || $host === '', RuntimeException::class, 'APP_URL must include a valid host before starting marketplace registration.');

        $domain = Str::lower($host);

        return Cache::lock('capell-extension-marketplace:marketplace-registration:' . hash('sha256', $domain), 10)
            ->block(5, fn (): MarketplaceRegistrationSession => $this->createAndPersistRegistrationSession($domain, $appUrl));
    }

    private function createAndPersistRegistrationSession(string $domain, string $appUrl): MarketplaceRegistrationSession
    {
        $registrationUrl = MarketplaceBaseUrl::resolve() . '/registration-sessions';
        $webhookUrl = MarketplaceWebhookUrl::resolve();

        throw_if($webhookUrl === null, RuntimeException::class, 'The marketplace webhook URL could not be resolved. Set APP_URL to this site URL or set CAPELL_MARKETPLACE_WEBHOOK_URL before connecting Marketplace.');

        $response = Http::timeout(config('capell-extension-marketplace.marketplace.timeout_seconds', 10))
            ->acceptJson()
            ->post($registrationUrl, [
                'domain' => $domain,
                'app_url' => $appUrl,
                'webhook_url' => $webhookUrl,
            ]);

        $response->throw();

        $data = $response->json('data');

        throw_unless(is_array($data), RuntimeException::class, 'The marketplace registration response did not include the expected data payload.');

        $marketplaceRegistrationId = $this->requiredIdentifier($data, 'registration_session_id');
        $challengeId = $this->requiredString($data, 'challenge_id');
        $challengeToken = $this->requiredString($data, 'challenge_token');
        $verificationUrl = $this->optionalString($data, 'verification_url');
        $expiresAt = $this->validatedExpiresAt($data);

        return $this->persistRegistrationSession(
            domain: $domain,
            marketplaceRegistrationId: $marketplaceRegistrationId,
            challengeId: $challengeId,
            challengeToken: $challengeToken,
            verificationUrl: $verificationUrl,
            expiresAt: $expiresAt,
        );
    }

    private function persistRegistrationSession(
        string $domain,
        string $marketplaceRegistrationId,
        string $challengeId,
        string $challengeToken,
        ?string $verificationUrl,
        CarbonImmutable $expiresAt,
    ): MarketplaceRegistrationSession {
        return DB::transaction(function () use ($domain, $marketplaceRegistrationId, $challengeId, $challengeToken, $verificationUrl, $expiresAt): MarketplaceRegistrationSession {
            MarketplaceRegistrationSession::query()
                ->where('domain', $domain)
                ->where('status', MarketplaceRegistrationStatus::Pending)
                ->lockForUpdate()
                ->update([
                    'status' => MarketplaceRegistrationStatus::Expired,
                    'expires_at' => now(),
                ]);

            $session = MarketplaceRegistrationSession::query()->create([
                'marketplace_registration_id' => $marketplaceRegistrationId,
                'domain' => $domain,
                'challenge_id' => $challengeId,
                'challenge_token' => $challengeToken,
                'verification_url' => $verificationUrl,
                'status' => MarketplaceRegistrationStatus::Pending,
                'expires_at' => $expiresAt,
            ]);

            MarketplaceRegistrationSession::query()
                ->where('domain', $domain)
                ->where('status', MarketplaceRegistrationStatus::Pending)
                ->whereKeyNot($session->getKey())
                ->update([
                    'status' => MarketplaceRegistrationStatus::Expired,
                    'expires_at' => now(),
                ]);

            return $session;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function requiredString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        throw_if(! is_string($value) || $value === '', RuntimeException::class, sprintf('The marketplace registration response did not include %s.', $key));

        return $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function requiredIdentifier(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if ((is_string($value) && $value !== '') || is_int($value)) {
            return (string) $value;
        }

        throw new RuntimeException(sprintf('The marketplace registration response did not include %s.', $key));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function optionalString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        throw_unless(is_string($value), RuntimeException::class, sprintf('The marketplace registration response included an invalid %s.', $key));

        return $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validatedExpiresAt(array $data): CarbonImmutable
    {
        $value = $this->requiredString($data, 'expires_at');

        try {
            $expiresAt = CarbonImmutable::parse($value);
        } catch (Throwable) {
            throw new RuntimeException('The marketplace registration response included an invalid expires_at.');
        }

        $maximumExpiry = now()->addMinutes(30);

        throw_if($expiresAt->isPast(), RuntimeException::class, 'The marketplace registration response included an expired challenge.');

        throw_if($expiresAt->greaterThan($maximumExpiry), RuntimeException::class, 'The marketplace registration response expiry is too far in the future.');

        return $expiresAt;
    }
}
