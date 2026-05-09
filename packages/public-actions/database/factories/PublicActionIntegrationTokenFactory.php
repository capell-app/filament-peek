<?php

declare(strict_types=1);

namespace Capell\PublicActions\Database\Factories;

use Capell\PublicActions\Enums\PublicActionIntegrationProvider;
use Capell\PublicActions\Enums\PublicActionIntegrationTokenAbility;
use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicActionIntegrationToken>
 */
class PublicActionIntegrationTokenFactory extends Factory
{
    protected $model = PublicActionIntegrationToken::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plainTextToken = $this->faker->uuid();

        return [
            'site_id' => null,
            'name' => $this->faker->words(2, true),
            'token_hash' => PublicActionIntegrationToken::hashPlainTextToken($plainTextToken),
            'provider' => PublicActionIntegrationProvider::Zapier,
            'abilities' => [
                PublicActionIntegrationTokenAbility::ListActions->value,
                PublicActionIntegrationTokenAbility::SubmitActions->value,
                PublicActionIntegrationTokenAbility::ReadSubmissions->value,
            ],
            'last_used_at' => null,
            'revoked_at' => null,
        ];
    }
}
