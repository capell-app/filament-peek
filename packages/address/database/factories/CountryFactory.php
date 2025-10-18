<?php

declare(strict_types=1);

namespace Capell\Core\Database\Factories;

use Capell\Address\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->country(),
            'iso2' => $this->faker->unique()->countryCode(),
            'iso3' => $this->faker->countryISOAlpha3(),
            'language_id' => null,
        ];
    }
}
