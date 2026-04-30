<?php

declare(strict_types=1);

namespace Capell\Analytics\Http\Controllers;

use Illuminate\Http\Response;

class AnalyticsBeaconController
{
    public function __invoke(): Response
    {
        return response()->noContent();
    }
}
