<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Filament\Extenders;

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Override;
use Pboivin\FilamentPeek\FilamentPeekPlugin;
use WeakMap;

final class FilamentPeekPanelExtender implements AdminPanelExtender
{
    /** @var WeakMap<Panel, true>|null */
    private static ?WeakMap $extendedPanels = null;

    #[Override]
    public function extend(Panel $panel): void
    {
        self::$extendedPanels ??= new WeakMap;
        if (isset(self::$extendedPanels[$panel])) {
            return;
        }

        // Registered so other packages (e.g. Publishing Studio's workspace
        // preview) can keep relying on the upstream Filament Peek modal.
        // The Capell page-preview action below renders its own modal and no
        // longer depends on it.
        if (! $panel->hasPlugin(FilamentPeekPlugin::ID)) {
            $panel->plugin(FilamentPeekPlugin::make());
        }

        $panel->renderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => view('capell-filament-peek::page-preview-modal')->render(),
        );

        // Direct late activation and shared synchronisation can resolve different extender instances.
        self::$extendedPanels[$panel] = true;
    }
}
