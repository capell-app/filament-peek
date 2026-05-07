<?php

declare(strict_types=1);

namespace Capell\Events\Database\Factories;

use Capell\Events\Models\EventVenue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventVenue>
 */
class EventVenueFactory extends Factory
{
    protected $model = EventVenue::class;

    public function definition(): array
    {
        return [
            'site_id' => null,
            'name' => 'Town Hall',
            'line1' => '1 High Street',
            'city' => 'Leeds',
            'postal_code' => 'LS1 1AA',
            'country' => 'United Kingdom',
            'status' => true,
        ];
    }
}
