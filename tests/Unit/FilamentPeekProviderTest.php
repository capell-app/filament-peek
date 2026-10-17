<?php

declare(strict_types=1);

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Capell\Admin\Contracts\Extenders\PagePreviewActionExtender;
use Capell\Core\Contracts\Extensions\RegistersExtensionRoute;
use Capell\FilamentPeek\Filament\Extenders\FilamentPeekPanelExtender;
use Capell\FilamentPeek\Manifest\FilamentPeekRoutesContribution;
use Capell\Tests\Support\InstalledRuntimeParity;
use Capell\Tests\Support\InstalledRuntimeState;
use Capell\Tests\Support\InstalledRuntimeTestCase;
use Filament\Actions\Action;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\ViewManager;
use Filament\View\PanelsRenderHook;
use Pboivin\FilamentPeek\FilamentPeekPlugin;

it('offers the configured preview plugin and labelled page action when installed', function (): void {
    $panel = Panel::make();
    foreach (app()->tagged(AdminPanelExtender::TAG) as $extender) {
        throw_unless($extender instanceof AdminPanelExtender, RuntimeException::class, 'Expected a panel extender.');
        $extender->extend($panel);
    }
    $actions = collect(app()->tagged(PagePreviewActionExtender::TAG))->flatMap(
        static function (object $extender): array {
            throw_unless($extender instanceof PagePreviewActionExtender, RuntimeException::class, 'Expected a preview action extender.');

            return $extender->actions();
        },
    );
    $preview = $actions->first(static fn (Action $action): bool => $action->getName() === 'peekPagePreview');
    throw_unless($preview instanceof Action, RuntimeException::class, 'Expected a preview action.');
    expect($panel->hasPlugin(FilamentPeekPlugin::make()->getId()))->toBeTrue()
        ->and($preview->getLabel())->toBe(__('capell-filament-peek::actions.preview.label'));
});

it('registers the peek plugin through the panel extender', function (): void {
    $panel = Panel::make();

    (new FilamentPeekPanelExtender)->extend($panel);

    expect($panel->hasPlugin(FilamentPeekPlugin::make()->getId()))->toBeTrue();
});

it('configures the Capell-owned device presets used by the redesigned preview modal', function (): void {
    expect(config('capell-filament-peek.preview.modal_device_presets.mobile.width'))->toBe('390px')
        ->and(config('capell-filament-peek.preview.modal_device_presets.tablet.rotatable'))->toBeTrue()
        ->and(config('capell-filament-peek.preview.modal_device_presets.desktop.rotatable'))->toBeFalse()
        ->and(config('capell-filament-peek.preview.modal_initial_device_preset'))->toBe('desktop');
});

it('still bridges Capell device presets into the upstream vendor modal config Publishing Studio depends on', function (): void {
    // PeekPagePreviewAction no longer reads these vendor `filament-peek.*`
    // keys (it has its own Capell-owned modal), but
    // WorkspacePeekPreviewAction in Publishing Studio calls
    // Peek::registerPreviewModal() directly against the *shared* upstream
    // modal and has no config wiring of its own. Without this bridge it
    // would silently fall back to the vendor package's own defaults
    // (a differently-named `tablet-landscape` preset at 1080x810, and a
    // 375x667 `mobile` preset), which are wrong for Capell.
    expect(config('filament-peek.devicePresets.fullscreen.icon'))->toBe('heroicon-o-computer-desktop')
        ->and(config('filament-peek.devicePresets.tablet.width'))->toBe('1024px')
        ->and(config('filament-peek.devicePresets.tablet.height'))->toBe('768px')
        ->and(config('filament-peek.devicePresets.tablet.canRotatePreset'))->toBeTrue()
        ->and(config('filament-peek.devicePresets.mobile.width'))->toBe('390px')
        ->and(config('filament-peek.devicePresets.mobile.height'))->toBe('844px')
        ->and(config('filament-peek.initialDevicePreset'))->toBe('fullscreen')
        ->and(config('filament-peek.devicePresets'))->not->toHaveKey('tablet-landscape');
});

it('registers the page preview modal render hook on the panel', function (): void {
    $panel = Panel::make();

    (new FilamentPeekPanelExtender)->extend($panel);

    FilamentView::swap(new ViewManager);
    $panel->boot();
    $rendered = FilamentView::renderHook(PanelsRenderHook::BODY_END)->toHtml();

    expect($rendered)->toContain('data-capell-page-preview-modal')
        ->and($rendered)->toContain(__('capell-filament-peek::actions.preview.devices.mobile'));
});

it('offers preview integrations only when the package is installed', function (bool $installed): void {
    InstalledRuntimeState::run(static function () use ($installed): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('filament-peek'), $installed, sourceRoot: dirname(__DIR__, 2));
        $panel = Panel::make();
        foreach ($app->tagged(AdminPanelExtender::TAG) as $extender) {
            throw_unless($extender instanceof AdminPanelExtender, RuntimeException::class, 'Expected a panel extender.');
            $extender->extend($panel);
        }
        $actions = [];
        foreach ($app->tagged(PagePreviewActionExtender::TAG) as $extender) {
            throw_unless($extender instanceof PagePreviewActionExtender, RuntimeException::class, 'Expected a preview action extender.');
            array_push($actions, ...$extender->actions());
        }
        expect($panel->hasPlugin(FilamentPeekPlugin::make()->getId()))->toBe($installed)
            ->and($actions)->toHaveCount($installed ? 1 : 0);
    });
})->with(['installed' => true, 'uninstalled' => false]);

it('declares the signed preview route contribution in the manifest', function (): void {
    $manifest = json_decode(
        (string) file_get_contents(__DIR__ . '/../../capell.json'),
        associative: true,
        flags: JSON_THROW_ON_ERROR,
    );

    throw_unless(is_array($manifest), RuntimeException::class, 'Expected Filament Peek manifest array.');
    throw_unless(is_array($manifest['contributes'] ?? null), RuntimeException::class, 'Expected Filament Peek contributions array.');
    throw_unless(is_array($manifest['security'] ?? null), RuntimeException::class, 'Expected Filament Peek security metadata array.');
    throw_unless(is_array($manifest['security']['publicSurface'] ?? null), RuntimeException::class, 'Expected Filament Peek public surface metadata array.');
    throw_unless(is_array($manifest['contributionTraceability'] ?? null), RuntimeException::class, 'Expected Filament Peek contribution traceability array.');

    $routeContribution = collect($manifest['contributes'])
        ->firstWhere('class', FilamentPeekRoutesContribution::class);

    throw_unless(is_array($routeContribution), RuntimeException::class, 'Expected Filament Peek route contribution array.');

    expect($routeContribution['type'])->toBe('route')
        ->and($routeContribution['routes'])->toBe(['capell-filament-peek.preview'])
        ->and($routeContribution['middleware'])->toBe(['web', 'signed'])
        ->and($routeContribution['tokenized'])->toBeTrue()
        ->and($manifest['security']['publicSurface']['tokenizedRoutes'])->toBe(['capell-filament-peek.preview'])
        ->and($manifest['contributionTraceability']['deferredContributions'])->toBe([])
        ->and(class_implements(FilamentPeekRoutesContribution::class))->toContain(RegistersExtensionRoute::class);
});
