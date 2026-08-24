<?php

declare(strict_types=1);

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Capell\Admin\Contracts\Extenders\PagePreviewActionExtender;
use Capell\Core\Contracts\Extensions\RegistersExtensionRoute;
use Capell\Core\Facades\CapellCore;
use Capell\FilamentPeek\Filament\Actions\PeekPagePreviewAction;
use Capell\FilamentPeek\Filament\Extenders\FilamentPeekPanelExtender;
use Capell\FilamentPeek\Filament\Extenders\PagePeekPreviewActionExtender;
use Capell\FilamentPeek\Manifest\FilamentPeekRoutesContribution;
use Capell\FilamentPeek\Providers\FilamentPeekServiceProvider;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Pboivin\FilamentPeek\FilamentPeekPlugin;

it('registers the panel and page preview extenders when installed', function (): void {
    $panelExtenders = collect(app()->tagged(AdminPanelExtender::TAG))
        ->map(fn (object $extender): string => $extender::class);

    $previewExtenders = collect(app()->tagged(PagePreviewActionExtender::TAG))
        ->map(fn (object $extender): string => $extender::class);

    expect($panelExtenders)->toContain(FilamentPeekPanelExtender::class)
        ->and($previewExtenders)->toContain(PagePeekPreviewActionExtender::class);
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

    $reflection = new ReflectionProperty($panel, 'renderHooks');
    $renderHooks = $reflection->getValue($panel);

    throw_unless(is_array($renderHooks), RuntimeException::class, 'Expected the panel render hooks property to be an array.');

    $bodyEndScopes = $renderHooks[PanelsRenderHook::BODY_END] ?? null;

    throw_unless(is_array($bodyEndScopes), RuntimeException::class, 'Expected registered body-end render hook scopes.');

    $bodyEndHooks = $bodyEndScopes[''] ?? null;

    throw_unless(is_array($bodyEndHooks), RuntimeException::class, 'Expected a registered body-end render hook.');

    $rendered = collect($bodyEndHooks)
        ->map(function (mixed $hook): string {
            throw_unless($hook instanceof Closure, RuntimeException::class, 'Expected each render hook to be a closure.');

            return (string) $hook();
        })
        ->implode('');

    expect($rendered)->toContain('data-capell-page-preview-modal')
        ->and($rendered)->toContain(__('capell-filament-peek::actions.preview.devices.mobile'));
});

it('does not boot runtime integrations when the package is not installed', function (): void {
    CapellCore::forcePackageInstalled(FilamentPeekServiceProvider::$packageName, false);

    $provider = new FilamentPeekServiceProvider(app());
    $reflection = new ReflectionMethod($provider, 'shouldRegisterRuntime');

    expect($reflection->invoke($provider))->toBeFalse();

    CapellCore::forcePackageInstalled(FilamentPeekServiceProvider::$packageName);
});

it('contributes the peek action to the page preview group', function (): void {
    $extender = new PagePeekPreviewActionExtender;

    expect($extender->actions()[0])->toBeInstanceOf(PeekPagePreviewAction::class);
});

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

    expect($routeContribution)->toBeArray()
        ->and($routeContribution['type'])->toBe('route')
        ->and($routeContribution['routes'])->toBe(['capell-filament-peek.preview'])
        ->and($routeContribution['middleware'])->toBe(['web', 'signed'])
        ->and($routeContribution['tokenized'])->toBeTrue()
        ->and($manifest['security']['publicSurface']['tokenizedRoutes'])->toBe(['capell-filament-peek.preview'])
        ->and($manifest['contributionTraceability']['deferredContributions'])->toBe([])
        ->and(class_implements(FilamentPeekRoutesContribution::class))->toContain(RegistersExtensionRoute::class);
});
