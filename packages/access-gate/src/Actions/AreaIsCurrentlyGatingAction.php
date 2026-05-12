<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Enums\AccessAreaStatus;
use Capell\AccessGate\Models\Area;
use Carbon\CarbonInterface;
use Lorisleiva\Actions\Concerns\AsAction;

final class AreaIsCurrentlyGatingAction
{
    use AsAction;

    public function handle(Area $area, ?CarbonInterface $now = null): bool
    {
        $now ??= now();

        if ($area->status !== AccessAreaStatus::Active) {
            return false;
        }

        if ($area->opens_at instanceof CarbonInterface && $area->opens_at->isAfter($now)) {
            return false;
        }

        if ($area->closes_at instanceof CarbonInterface && $area->closes_at->isBefore($now)) {
            return false;
        }

        return true;
    }
}
