<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Providers;

use Capell\Admin\Enums\SchemaTypeEnum;
use Capell\Admin\Facades\CapellAdmin;
use Capell\DefaultTheme\Filament\Schemas\Themes\DefaultThemeSchema;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        CapellAdmin::registerSchema(SchemaTypeEnum::Theme, DefaultThemeSchema::class);
    }

    public function register(): void {}
}
