<?php

declare(strict_types=1);

namespace Capell\Events\Support\Calendar;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class CalendarMonth
{
    /**
     * @return Collection<int, CalendarWeek>
     */
    public function weeks(CarbonImmutable $month): Collection
    {
        $firstVisibleDay = $month->startOfMonth()->startOfWeek();
        $lastVisibleDay = $month->endOfMonth()->endOfWeek();
        $weeks = collect();
        $cursor = $firstVisibleDay;

        while ($cursor->lessThanOrEqualTo($lastVisibleDay)) {
            $days = collect();

            for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                $days->push($cursor);
                $cursor = $cursor->addDay();
            }

            $weeks->push(new CalendarWeek($days));
        }

        return $weeks;
    }
}
