<?php

declare(strict_types=1);

namespace Capell\PublicActions\Database\Factories;

use Capell\PublicActions\Enums\PublicActionDestinationStatus;
use Capell\PublicActions\Models\PublicAction;
use Capell\PublicActions\Models\PublicActionDestination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicActionDestination>
 */
class PublicActionDestinationFactory extends Factory
{
    protected $model = PublicActionDestination::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_action_id' => PublicAction::factory(),
            'adapter' => 'http_webhook',
            'name' => $this->faker->words(2, true),
            'status' => PublicActionDestinationStatus::Active,
            'endpoint_url' => 'https://hooks.example.test/' . $this->faker->uuid(),
            'secret' => $this->faker->sha256(),
            'headers' => [
                'X-Capell-Test' => 'true',
            ],
            'settings' => [
                'method' => 'POST',
            ],
        ];
    }
}
