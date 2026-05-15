<?php

declare(strict_types=1);

use Capell\PublishingStudio\Http\Controllers\ExitWorkspacePreviewController;
use Capell\PublishingStudio\Http\Controllers\SchedulerIcalFeedController;
use Illuminate\Support\Facades\Route;

Route::get('capell/preview/exit', ExitWorkspacePreviewController::class)
    ->name('capell-frontend.preview.exit');

Route::get('capell/publishing-studio/scheduler/ical/{token}', SchedulerIcalFeedController::class)
    ->where('token', '[A-Za-z0-9]+')
    ->name('capell-publishing-studio.scheduler.ical');
