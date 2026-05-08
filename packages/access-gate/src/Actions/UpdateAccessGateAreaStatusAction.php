<?php

declare(strict_types=1);

namespace Capell\AccessGate\Actions;

use Capell\AccessGate\Enums\AccessAreaStatus;
use Capell\AccessGate\Models\Area;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpdateAccessGateAreaStatusAction
{
    use AsAction;

    public function handle(Area $area, AccessAreaStatus $status): Area
    {
        $area->forceFill([
            'status' => $status,
        ])->save();

        return $area;
    }
}
