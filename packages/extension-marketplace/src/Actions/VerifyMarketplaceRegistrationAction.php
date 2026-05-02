<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Actions;

use Capell\ExtensionMarketplace\Enums\MarketplaceRegistrationStatus;
use Capell\ExtensionMarketplace\Models\MarketplaceInstance;
use Capell\ExtensionMarketplace\Models\MarketplaceRegistrationSession;
use Capell\ExtensionMarketplace\Support\MarketplaceBaseUrl;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;

final class VerifyMarketplaceRegistrationAction
{
    use AsAction;

    public function handle(MarketplaceRegistrationSession $session): MarketplaceInstance
    {
        throw_unless(in_array($session->status, [MarketplaceRegistrationStatus::Pending, MarketplaceRegistrationStatus::Approved], true), RuntimeException::class, 'Only pending marketplace registrations can be verified.');

        if ($session->expires_at === null || $session->expires_at->isPast()) {
            $session->update(['status' => MarketplaceRegistrationStatus::Expired]);

            throw new RuntimeException('The marketplace registration challenge has expired.');
        }

        $response = Http::timeout(config('capell-extension-marketplace.marketplace.timeout_seconds', 10))
            ->acceptJson()
            ->post(MarketplaceBaseUrl::resolve() . '/registration-sessions/' . $session->marketplace_registration_id . '/verify');

        $response->throw();

        $data = $response->json('data');

        throw_unless(is_array($data), RuntimeException::class, 'Marketplace did not return verified instance data.');

        $instance = MarketplaceInstance::query()->updateOrCreate(
            ['instance_id' => $this->requiredString($data, 'instance_id')],
            [
                'signing_secret_encrypted' => $this->requiredString($data, 'signing_secret'),
                'last_heartbeat_at' => now(),
            ],
        );

        $session->update([
            'status' => MarketplaceRegistrationStatus::Verified,
            'verified_at' => now(),
        ]);

        return $instance;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function requiredString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        throw_if(! is_string($value) || $value === '', RuntimeException::class, sprintf('Marketplace did not return %s.', $key));

        return $value;
    }
}
