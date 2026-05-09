<?php

declare(strict_types=1);

namespace Capell\PublicActions\Actions;

use Capell\PublicActions\Data\PublicActionIntegrationTokenData;
use Capell\PublicActions\Enums\PublicActionIntegrationProvider;
use Capell\PublicActions\Enums\PublicActionIntegrationTokenAbility;
use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreatePublicActionIntegrationTokenAction
{
    use AsAction;

    /**
     * @param  list<PublicActionIntegrationTokenAbility>  $abilities
     */
    public function handle(
        string $name,
        PublicActionIntegrationProvider $provider = PublicActionIntegrationProvider::Zapier,
        ?int $siteId = null,
        array $abilities = [],
    ): PublicActionIntegrationTokenData {
        $resolvedAbilities = $abilities === []
            ? PublicActionIntegrationTokenAbility::cases()
            : $abilities;
        $plainTextToken = 'cpa_' . Str::random(64);

        $token = PublicActionIntegrationToken::query()->create([
            'site_id' => $siteId,
            'name' => $name,
            'token_hash' => PublicActionIntegrationToken::hashPlainTextToken($plainTextToken),
            'provider' => $provider,
            'abilities' => array_map(
                static fn (PublicActionIntegrationTokenAbility $ability): string => $ability->value,
                $resolvedAbilities,
            ),
        ]);

        return new PublicActionIntegrationTokenData(
            plainTextToken: $plainTextToken,
            token: $token,
            abilities: $token->abilities ?? [],
        );
    }
}
