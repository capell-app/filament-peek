<?php

declare(strict_types=1);

namespace Capell\Forms\Database\Factories;

use Capell\Forms\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'site_id' => null,
            'name' => Str::headline($name),
            'handle' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'schema' => [
                [
                    'key' => 'email',
                    'label' => 'Email',
                    'type' => 'email',
                    'required' => true,
                    'validation_rules' => ['email'],
                ],
            ],
            'settings' => [
                'success_message' => null,
                'store_submissions' => true,
                'notification_email' => null,
                'collect_ip_address' => true,
                'collect_user_agent' => true,
            ],
            'is_active' => true,
        ];
    }
}
