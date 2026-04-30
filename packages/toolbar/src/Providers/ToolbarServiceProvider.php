<?php

declare(strict_types=1);

namespace Capell\Toolbar\Providers;

use Capell\Toolbar\Http\Middleware\PassThroughActivityMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ToolbarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'capell-frontend-toolbar');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'capell');
        $this->registerFallbackMiddlewareAliases();
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/capell-frontend-toolbar.php', 'capell-frontend-toolbar');
    }

    private function registerFallbackMiddlewareAliases(): void
    {
        if (array_key_exists('frontend.activity', Route::getMiddleware())) {
            return;
        }

        Route::aliasMiddleware('frontend.activity', PassThroughActivityMiddleware::class);
    }
}
