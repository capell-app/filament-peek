<?php

declare(strict_types=1);

namespace Capell\PublicActions\Database\Factories;

use Capell\PublicActions\Enums\PublicActionDispatchStatus;
use Capell\PublicActions\Models\PublicActionDestination;
use Capell\PublicActions\Models\PublicActionDispatchAttempt;
use Capell\PublicActions\Models\PublicActionSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicActionDispatchAttempt>
 */
class PublicActionDispatchAttemptFactory extends Factory
{
    protected $model = PublicActionDispatchAttempt::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_action_submission_id' => PublicActionSubmission::factory(),
            'public_action_destination_id' => PublicActionDestination::factory(),
            'adapter' => 'http_webhook',
            'status' => PublicActionDispatchStatus::Pending,
            'attempt' => 1,
            'request_hash' => hash('sha256', $this->faker->uuid()),
            'response_status' => null,
            'response_summary' => null,
            'error_message' => null,
            'dispatched_at' => null,
        ];
    }
}
