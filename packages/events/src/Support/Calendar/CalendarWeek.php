<?php

declare(strict_types=1);

namespace Capell\Events\Support\Calendar;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class CalendarWeek
{
    /**
     * @param  Collection<int, CarbonImmutable>  $days
     */
    public function __construct(public readonly Collection $days) {}
}
