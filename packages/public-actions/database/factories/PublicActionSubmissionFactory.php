<?php

declare(strict_types=1);

namespace Capell\PublicActions\Database\Factories;

use Capell\PublicActions\Enums\PublicActionSubmissionStatus;
use Capell\PublicActions\Models\PublicAction;
use Capell\PublicActions\Models\PublicActionSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicActionSubmission>
 */
class PublicActionSubmissionFactory extends Factory
{
    protected $model = PublicActionSubmission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_action_id' => PublicAction::factory(),
            'site_id' => null,
            'source_type' => null,
            'source_id' => null,
            'payload' => [
                'email' => $this->faker->safeEmail(),
            ],
            'metadata' => [
                'ip_hash' => hash('sha256', $this->faker->ipv4()),
                'url' => $this->faker->url(),
            ],
            'status' => PublicActionSubmissionStatus::Received,
            'submitted_at' => now(),
        ];
    }
}
