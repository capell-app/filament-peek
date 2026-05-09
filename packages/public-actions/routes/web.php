<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix(config('capell-public-actions.route_prefix', 'actions'))
    ->as('capell-public-actions.')
    ->group(function (): void {
        //
    });
