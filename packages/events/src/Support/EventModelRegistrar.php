<?php

declare(strict_types=1);

namespace Capell\Events\Support;

use Capell\Core\Data\PageVariationData;
use Capell\Core\Facades\CapellCore;
use Capell\Events\Models\Event;
use Capell\Events\Models\EventNotificationLog;
use Capell\Events\Models\EventOccurrence;
use Capell\Events\Models\EventRegistration;
use Capell\Events\Models\EventVenue;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class EventModelRegistrar
{
    /** @var list<class-string> */
    private const MODELS = [
        Event::class,
        EventVenue::class,
        EventOccurrence::class,
        EventRegistration::class,
        EventNotificationLog::class,
    ];

    public static function register(): void
    {
        CapellCore::registerModels(self::MODELS);

        CapellCore::registerPageVariation(
            new PageVariationData(
                name: 'event',
                model: Event::class,
                resourceName: 'event',
            ),
        );

        Relation::morphMap(
            collect(self::MODELS)
                ->mapWithKeys(fn (string $modelClass): array => [Str::snake(class_basename($modelClass)) => $modelClass])
                ->all(),
            merge: true,
        );
    }
}
