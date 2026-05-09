<?php

declare(strict_types=1);

namespace Capell\PublicActions\Database\Factories;

use Capell\PublicActions\Enums\PublicActionStatus;
use Capell\PublicActions\Models\PublicAction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicAction>
 */
class PublicActionFactory extends Factory
{
    protected $model = PublicAction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = $this->faker->unique()->slug(2);

        return [
            'site_id' => null,
            'site_scope_key' => 'global',
            'key' => $key,
            'name' => $this->faker->words(3, true),
            'status' => PublicActionStatus::Active,
            'handler_class' => 'App\\Actions\\' . str_replace(' ', '', $this->faker->words(2, true)) . 'Action',
            'success_redirect_url' => null,
            'failure_redirect_url' => null,
            'success_message' => null,
            'failure_message' => null,
            'payload_schema' => [
                'fields' => [
                    ['key' => 'email', 'type' => 'email', 'required' => true],
                ],
            ],
            'settings' => [],
        ];
    }
}
