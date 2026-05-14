<?php

declare(strict_types=1);

namespace Capell\Events\Database\Factories;

use Capell\Core\Enums\TypeEnum;
use Capell\Core\Models\Language;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Site;
use Capell\Core\Models\Theme;
use Capell\Core\Models\Type;
use Capell\Events\Enums\EventBookingModeEnum;
use Capell\Events\Enums\EventLocationModeEnum;
use Capell\Events\Enums\EventVisibilityEnum;
use Capell\Events\Models\Event;
use Capell\Events\Models\EventVenue;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $startsAt = CarbonImmutable::now()->addWeek()->setTime(10, 0);

        return [
            'site_id' => fn (): int => $this->site()->id,
            'blueprint_id' => fn (): int => $this->type('event', TypeEnum::Page->value)->id,
            'layout_id' => fn (): int => Layout::query()->firstOrCreate([
                'key' => 'event',
            ], [
                'name' => 'Event',
            ])->id,
            'event_venue_id' => EventVenue::factory(),
            'name' => 'Community event',
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHours(2),
            'timezone' => 'UTC',
            'all_day' => false,
            'visibility' => EventVisibilityEnum::Public->value,
            'location_mode' => EventLocationModeEnum::Venue->value,
            'booking_mode' => EventBookingModeEnum::NativeRsvp->value,
            'capacity' => 20,
            'waitlist_enabled' => true,
        ];
    }

    private function site(): Site
    {
        $language = Language::query()->firstOrCreate([
            'code' => 'en',
        ], [
            'name' => 'English',
            'default' => true,
            'status' => true,
        ]);

        $theme = Theme::query()->firstOrCreate([
            'key' => 'default',
        ], [
            'name' => 'Default',
            'blueprint_id' => $this->type('default', TypeEnum::Theme->value)->id,
            'default' => true,
            'status' => true,
        ]);

        return Site::query()->firstOrCreate([
            'name' => 'Default site',
        ], [
            'blueprint_id' => $this->type('default', TypeEnum::Site->value)->id,
            'theme_id' => $theme->id,
            'language_id' => $language->id,
            'default' => true,
            'status' => true,
        ]);
    }

    private function type(string $key, string $type): Type
    {
        return Type::query()->firstOrCreate([
            'key' => $key,
            'type' => $type,
        ], [
            'name' => ucfirst($key),
        ]);
    }
}
