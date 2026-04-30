<?php

declare(strict_types=1);

use Capell\Analytics\Http\Controllers\AnalyticsBeaconController;
use Capell\Analytics\Http\Controllers\AnalyticsConsentController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

$routePrefix = trim((string) config('capell-analytics.route_prefix', 'capell/analytics'), '/');

Route::prefix($routePrefix)
    ->middleware(['web'])
    ->group(function (): void {
        Route::post('events', AnalyticsBeaconController::class)
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->name('capell-analytics.events');

        Route::post('consent', AnalyticsConsentController::class)
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->name('capell-analytics.consent');
    });
