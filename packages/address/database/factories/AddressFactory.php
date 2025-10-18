<?php

declare(strict_types=1);

namespace Capell\Core\Database\Factories;

use Capell\Address\Models\Address;
use Capell\Address\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'line1' => $this->faker->streetAddress(),
            'line2' => null,
            'city' => $this->faker->city(),
            'state' => null,
            'postal_code' => $this->faker->postal_code(),
            'country_id' => Country::factory(),
            'meta' => [
                'latitude' => $this->faker->latitude(),
                'longitude' => $this->faker->longitude(),
            ],
        ];
    }
}
