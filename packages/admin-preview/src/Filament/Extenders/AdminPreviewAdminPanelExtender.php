<?php

declare(strict_types=1);

namespace Capell\AdminPreview\Filament\Extenders;

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Filament\Panel;
use Pboivin\FilamentPeek\FilamentPeekPlugin;

final class AdminPreviewAdminPanelExtender implements AdminPanelExtender
{
    public function extend(Panel $panel): void
    {
        if ($panel->hasPlugin(FilamentPeekPlugin::ID)) {
            return;
        }

        $panel->plugin(FilamentPeekPlugin::make());
    }
}
