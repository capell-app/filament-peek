<?php

declare(strict_types=1);

use Capell\Events\Http\Controllers\CalendarFeedController;
use Illuminate\Support\Facades\Route;

Route::get('events.ics', CalendarFeedController::class)->name('calendar-feed');
Route::get('events/{listingPage}/feed.ics', CalendarFeedController::class)->name('listing-calendar-feed');
