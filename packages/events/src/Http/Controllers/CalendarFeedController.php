<?php

declare(strict_types=1);

namespace Capell\Events\Http\Controllers;

use Capell\Events\Actions\BuildCalendarFeedAction;
use Capell\Frontend\Facades\Frontend;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class CalendarFeedController extends BaseController
{
    public function __invoke(): Response
    {
        $site = Frontend::site();

        return response(BuildCalendarFeedAction::run($site), 200, [
            'Content-Disposition' => 'inline; filename="events.ics"',
            'Content-Type' => 'text/calendar; charset=UTF-8',
        ]);
    }
}
