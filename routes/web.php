<?php

declare(strict_types=1);

use Capell\FilamentPeek\Http\Controllers\PagePreviewController;
use Illuminate\Support\Facades\Route;

$middleware = config('capell-filament-peek.preview.middleware', ['web', 'signed']);

if (! is_array($middleware) && ! is_string($middleware) && $middleware !== null) {
    throw new UnexpectedValueException('Preview middleware must be an array, a string or null.');
}

Route::middleware($middleware)
    ->prefix((string) config('capell-filament-peek.preview.route_prefix', 'capell-filament-peek'))
    ->name('capell-filament-peek.')
    ->group(function (): void {
        Route::get('/preview/{token}', PagePreviewController::class)
            ->name('preview');
    });
