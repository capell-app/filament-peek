<?php

declare(strict_types=1);

namespace Capell\Themes\Core;

use Capell\Core\Actions\RegisterBlazeOptimizedViewsAction;
use Illuminate\Support\ServiceProvider;

final class ThemesCoreServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'capell-themes-core');

        RegisterBlazeOptimizedViewsAction::run(__DIR__ . '/../resources/views/components');
    }
}
