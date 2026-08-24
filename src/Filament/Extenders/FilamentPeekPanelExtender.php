<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Filament\Extenders;

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Pboivin\FilamentPeek\FilamentPeekPlugin;

final class FilamentPeekPanelExtender implements AdminPanelExtender
{
    public function extend(Panel $panel): void
    {
        // Registered so other packages (e.g. Publishing Studio's workspace
        // preview) can keep relying on the upstream Filament Peek modal.
        // The Capell page-preview action below renders its own modal and no
        // longer depends on it.
        $panel->plugin(FilamentPeekPlugin::make());

        $panel->renderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => view('capell-filament-peek::page-preview-modal')->render(),
        );
    }
}
