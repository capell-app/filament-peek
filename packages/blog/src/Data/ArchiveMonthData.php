<?php

declare(strict_types=1);

namespace Capell\Blog\Data;

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Spatie\LaravelData\Data;

class ArchiveMonthData extends Data
{
    public function __construct(
        public int $year,
        public int $month,
        public ?int $total = null,
    ) {}

    public function getDate(): Carbon
    {
        return Date::createFromFormat('Y-m', sprintf('%d-%d', $this->year, $this->month));
    }
}
