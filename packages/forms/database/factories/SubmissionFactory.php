<?php

declare(strict_types=1);

namespace Capell\Forms\Database\Factories;

use Capell\Forms\Enums\SubmissionStatus;
use Capell\Forms\Models\Form;
use Capell\Forms\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'site_id' => null,
            'payload' => [
                'values' => [
                    'email' => $this->faker->safeEmail(),
                ],
            ],
            'meta' => [
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Forms test agent',
                'url' => 'https://example.test/contact',
                'referer' => null,
            ],
            'status' => SubmissionStatus::New,
            'submitted_at' => now(),
        ];
    }
}
