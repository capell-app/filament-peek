<?php

declare(strict_types=1);

namespace Capell\FilamentPeek\Providers;

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Capell\Admin\Contracts\Extenders\PagePreviewActionExtender;
use Capell\Admin\Filament\Plugin\CapellAdminPlugin;
use Capell\Admin\Http\Middleware\EnsureInstalledPanelAvailable;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\Core\Support\Packages\RouteGroupIsolation;
use Capell\FilamentPeek\Filament\Extenders\FilamentPeekPanelExtender;
use Capell\FilamentPeek\Filament\Extenders\PagePeekPreviewActionExtender;
use Filament\Facades\Filament;
use Illuminate\Routing\Router;
use Override;
use Spatie\LaravelPackageTools\Package;

final class FilamentPeekServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-filament-peek';

    public static string $packageName = 'capell-app/filament-peek';

    #[Override]
    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasRoute('web');
    }

    #[Override]
    public function packageRegistered(): void
    {
        $this->configureUpstreamPreviewModal();
    }

    #[Override]
    protected function bootPackageRoutes(): self
    {
        if (! $this->isPackageInstalled()) {
            $this->registerRoutes();
        }

        return $this;
    }

    #[Override]
    protected function bootInstalledRuntime(): void
    {
        $this->registerRoutes();

        if (! config('capell-filament-peek.enabled', true)) {
            return;
        }

        $this->app->tag([FilamentPeekPanelExtender::class], AdminPanelExtender::TAG);
        $this->app->tag([PagePeekPreviewActionExtender::class], PagePreviewActionExtender::TAG);

        if ($this->app->isBooted()) {
            // CapellAdminPlugin synchronises its own panels after runtime refresh.
            // InstalledPanelRuntime marks participating panels with its availability middleware.
            // Extend only those bare panels: shared synchronisation also changes route middleware.
            foreach (Filament::getPanels() as $panel) {
                if (! $panel->hasPlugin(CapellAdminPlugin::ID)
                    && in_array(EnsureInstalledPanelAvailable::class . ':' . $panel->getId(), $panel->getMiddleware(), true)) {
                    $this->app->make(FilamentPeekPanelExtender::class)->extend($panel);
                }
            }
        }
    }

    private function registerRoutes(): void
    {
        // Dependants need canonical preview routes before provider boot callbacks.
        RouteGroupIsolation::run($this->app->make(Router::class), function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        });
    }

    /**
     * Bridges Capell's own device-preset config into the vendor
     * pboivin/filament-peek config keys its shared modal reads directly.
     * PeekPagePreviewAction no longer needs this (it renders its own
     * Capell-owned modal), but Publishing Studio's
     * WorkspacePeekPreviewAction still opens the upstream modal via
     * Peek::registerPreviewModal() and has no config wiring of its own,
     * so this must keep running regardless of that action's changes.
     */
    private function configureUpstreamPreviewModal(): void
    {
        $devicePresets = config('capell-filament-peek.preview.device_presets', false);

        config()->set('filament-peek.devicePresets', is_array($devicePresets) ? $devicePresets : false);
        config()->set('filament-peek.initialDevicePreset', $this->configString('capell-filament-peek.preview.initial_device_preset', 'fullscreen'));
        config()->set('filament-peek.allowIframeOverflow', $this->configBool('capell-filament-peek.preview.allow_iframe_overflow', false));
        config()->set('filament-peek.allowIframePointerEvents', $this->configBool('capell-filament-peek.preview.allow_iframe_pointer_events', false));
    }

    private function configString(string $key, string $default): string
    {
        $value = config($key, $default);

        return is_string($value) && $value !== '' ? $value : $default;
    }

    private function configBool(string $key, bool $default): bool
    {
        $value = config($key, $default);

        return is_bool($value) ? $value : $default;
    }
}
