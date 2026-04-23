<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Providers;

use Capell\Core\Enums\ModelEnum;
use Capell\Core\Facades\CapellCore;
use Capell\DefaultTheme\Support\Interceptors\Themes\DefaultThemeInterceptor;
use Illuminate\Support\ServiceProvider;

class DefaultThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $themeModel = CapellCore::getModel(ModelEnum::Theme);
        CapellCore::registerModelInterceptor($themeModel, interceptorClass: DefaultThemeInterceptor::class);
    }

    public function register(): void {}
}
