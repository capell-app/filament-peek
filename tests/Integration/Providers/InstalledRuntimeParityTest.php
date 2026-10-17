<?php

declare(strict_types=1);

use Capell\Admin\Contracts\Extenders\AdminPanelExtender;
use Capell\Admin\Contracts\Extenders\PagePreviewActionExtender;
use Capell\Admin\Filament\Plugin\CapellAdminPlugin;
use Capell\Admin\Support\InstalledPanelRuntime;
use Capell\Core\Actions\DisablePackageAction;
use Capell\Core\Actions\EnablePackageAction;
use Capell\Core\Actions\InstallPackageAction;
use Capell\Core\Actions\RuntimeRefresh\RefreshInstalledPackageRuntimeAction;
use Capell\Core\Facades\CapellCore;
use Capell\FilamentPeek\Filament\Extenders\FilamentPeekPanelExtender;
use Capell\FilamentPeek\Filament\Extenders\PagePeekPreviewActionExtender;
use Capell\FilamentPeek\Providers\FilamentPeekServiceProvider;
use Capell\Tests\Support\InstalledRuntimeParity;
use Capell\Tests\Support\InstalledRuntimeState;
use Capell\Tests\Support\InstalledRuntimeTestCase;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\PanelsRenderHook;
use Illuminate\Foundation\Application;
use Pboivin\FilamentPeek\Facades\Peek;
use Pboivin\FilamentPeek\FilamentPeekPlugin;

it('matches fresh installed boot after in-process installation and repeated refresh', function (): void {
    InstalledRuntimeParity::assertPackage('filament-peek');
});

it('registers the upstream plugin assets on fresh boot and late installation', function (bool $installed): void {
    InstalledRuntimeState::run(static function () use ($installed): void {
        $app = InstalledRuntimeTestCase::boot(
            InstalledRuntimeParity::manifest('filament-peek'),
            $installed,
            static function (): void {
                Panel::configureUsing(static fn (Panel $panel): Panel => $panel->plugin(CapellAdminPlugin::make()));
            },
            sourceRoot: dirname(__DIR__, 3),
        );

        if (! $installed) {
            expect(Filament::getPanel('admin')->hasPlugin(FilamentPeekPlugin::ID))->toBeFalse()
                ->and(FilamentAsset::getScripts([FilamentPeekPlugin::PACKAGE]))->toBe([])
                ->and(FilamentAsset::getStyles([FilamentPeekPlugin::PACKAGE]))->toBe([]);

            InstalledRuntimeTestCase::prepareLateInstallMigrations($app);
            InstallPackageAction::run(CapellCore::getPackage(FilamentPeekServiceProvider::$packageName));
        }

        expect(Filament::getPanel('admin')->hasPlugin(FilamentPeekPlugin::ID))->toBeTrue()
            ->and(array_map(static fn (Asset $asset): string => $asset->getId(), FilamentAsset::getScripts([FilamentPeekPlugin::PACKAGE])))->toBe([FilamentPeekPlugin::ID])
            ->and(array_map(static fn (Asset $asset): string => $asset->getId(), FilamentAsset::getStyles([FilamentPeekPlugin::PACKAGE])))->toBe([FilamentPeekPlugin::ID]);
    });
})->with(['fresh boot' => true, 'late installation' => false]);

it('renders each bare panel preview modal once after late installation and shared synchronisation', function (bool $upstreamAttached): void {
    InstalledRuntimeState::run(static function () use ($upstreamAttached): void {
        $app = InstalledRuntimeTestCase::boot(
            InstalledRuntimeParity::manifest('filament-peek'),
            false,
            static function (Application $app): void {
                $app->register(Pboivin\FilamentPeek\FilamentPeekServiceProvider::class);
            },
            sourceRoot: dirname(__DIR__, 3),
        );
        $panel = Filament::getPanel('admin');
        expect($panel->hasPlugin(CapellAdminPlugin::ID))->toBeFalse()
            ->and($panel->hasPlugin(FilamentPeekPlugin::ID))->toBeFalse();

        if ($upstreamAttached) {
            $panel->plugin(FilamentPeekPlugin::make());
        }

        Peek::registerPreviewModal();
        InstalledRuntimeTestCase::prepareLateInstallMigrations($app);
        InstallPackageAction::run(CapellCore::getPackage(FilamentPeekServiceProvider::$packageName));

        $assertModals = static function () use ($panel): void {
            /** @var array<string, array<string, list<Closure>>> $hooks */
            $hooks = new ReflectionProperty($panel, 'renderHooks')->getValue($panel);
            $bodyEnd = $hooks[PanelsRenderHook::BODY_END][''] ?? [];
            $html = implode('', array_map(static fn (Closure $hook): string => (string) $hook(), $bodyEnd));
            throw_if($html === '', RuntimeException::class, 'Expected preview modal HTML.');
            $dom = new DOMDocument;
            $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new DOMXPath($dom);

            expect($xpath->query('//*[@data-capell-page-preview-modal]'))->toHaveCount(1)
                ->and($xpath->query('//*[@aria-labelledby="filament-peek-modal-title"]'))->toHaveCount(1)
                ->and($bodyEnd)->toHaveCount(2);
        };

        $assertModals();
        $app->make(InstalledPanelRuntime::class)->extend($panel);
        $assertModals();
    });
})->with(['bare panel' => false, 'upstream plugin already attached' => true]);

it('leaves unrelated panels unchanged during late installation and repeated refresh', function (): void {
    InstalledRuntimeState::run(static function (): void {
        $app = InstalledRuntimeTestCase::boot(
            InstalledRuntimeParity::manifest('filament-peek'),
            false,
            static function (Application $app): void {
                $app->register(Pboivin\FilamentPeek\FilamentPeekServiceProvider::class);
            },
            sourceRoot: dirname(__DIR__, 3),
        );
        $panel = Filament::getPanel('admin');
        expect($panel->hasPlugin(CapellAdminPlugin::ID))->toBeFalse();
        $customer = Panel::make()->id('customer')->path('customer')
            ->middleware(['customer-only'])
            ->renderHook(PanelsRenderHook::BODY_END, static fn (): string => '<div>Customer content</div>');
        $app->make(PanelRegistry::class)->register($customer);

        $customerState = static fn (): array => [
            $customer->getPlugins(),
            new ReflectionProperty($customer, 'renderHooks')->getValue($customer),
            $customer->getMiddleware(),
            $customer->getAuthMiddleware(),
            $customer->getTenantMiddleware(),
            $customer->getResources(),
            $customer->getPages(),
            $customer->getWidgets(),
        ];
        $before = $customerState();
        $routesBefore = InstalledRuntimeParity::routeInventory($app);
        $nonPeekRoutes = static fn (array $routes): array => array_values(array_filter(
            $routes,
            static fn (array $route): bool => ! str_contains((string) $route[0], 'capell-filament-peek'),
        ));
        $middlewareBefore = $panel->getMiddleware();
        Peek::registerPreviewModal();
        InstalledRuntimeTestCase::prepareLateInstallMigrations($app);
        $package = CapellCore::getPackage(FilamentPeekServiceProvider::$packageName);
        InstallPackageAction::run($package);

        $assertPanels = static function () use ($app, $panel, $customerState, $before, $middlewareBefore, $routesBefore, $nonPeekRoutes): void {
            expect($customerState())->toBe($before)
                ->and($panel->getMiddleware())->toBe($middlewareBefore)
                ->and($nonPeekRoutes(InstalledRuntimeParity::routeInventory($app)))->toBe($nonPeekRoutes($routesBefore))
                ->and($panel->hasPlugin(FilamentPeekPlugin::ID))->toBeTrue()
                ->and(FilamentAsset::getScripts([FilamentPeekPlugin::PACKAGE]))->toHaveCount(1)
                ->and(FilamentAsset::getStyles([FilamentPeekPlugin::PACKAGE]))->toHaveCount(1);

            /** @var array<string, array<string, list<Closure>>> $hooks */
            $hooks = new ReflectionProperty($panel, 'renderHooks')->getValue($panel);
            $bodyEnd = $hooks[PanelsRenderHook::BODY_END][''] ?? [];
            $html = implode('', array_map(static fn (Closure $hook): string => (string) $hook(), $bodyEnd));
            throw_if($html === '', RuntimeException::class, 'Expected preview modal HTML.');
            $dom = new DOMDocument;
            $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new DOMXPath($dom);

            expect($xpath->query('//*[@data-capell-page-preview-modal]'))->toHaveCount(1)
                ->and($xpath->query('//*[@aria-labelledby="filament-peek-modal-title"]'))->toHaveCount(1)
                ->and($bodyEnd)->toHaveCount(2);
        };

        $assertPanels();
        $scripts = FilamentAsset::getScripts([FilamentPeekPlugin::PACKAGE]);
        $styles = FilamentAsset::getStyles([FilamentPeekPlugin::PACKAGE]);
        foreach ([1, 2] as $refresh) {
            RefreshInstalledPackageRuntimeAction::run($package);
            $assertPanels();
            expect(FilamentAsset::getScripts([FilamentPeekPlugin::PACKAGE]))->toBe($scripts)
                ->and(FilamentAsset::getStyles([FilamentPeekPlugin::PACKAGE]))->toBe($styles);
        }
    });
});

it('retains exactly one of each extender through repeated registration and disable enable', function (): void {
    InstalledRuntimeState::run(static function (): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('filament-peek'), true, sourceRoot: dirname(__DIR__, 3));
        $package = CapellCore::getPackage(FilamentPeekServiceProvider::$packageName);

        $assertExtenders = static function (Application $app): void {
            $panels = array_values(array_filter(iterator_to_array($app->tagged(AdminPanelExtender::TAG)), static fn (object $extender): bool => $extender instanceof FilamentPeekPanelExtender));
            $previews = array_values(array_filter(iterator_to_array($app->tagged(PagePreviewActionExtender::TAG)), static fn (object $extender): bool => $extender instanceof PagePeekPreviewActionExtender));

            expect($panels)->toHaveCount(1)
                ->and($previews)->toHaveCount(1)
                ->and($previews[0]->actions())->toHaveCount(1);
        };

        $assertExtenders($app);
        $app->register(FilamentPeekServiceProvider::class, force: true);
        $app->register(FilamentPeekServiceProvider::class, force: true);
        RefreshInstalledPackageRuntimeAction::run($package);
        RefreshInstalledPackageRuntimeAction::run($package);
        $assertExtenders($app);

        DisablePackageAction::run($package);
        expect(CapellCore::isPackageEnabled($package->name))->toBeFalse();
        EnablePackageAction::run($package);
        expect(CapellCore::isPackageEnabled($package->name))->toBeTrue();
        $assertExtenders($app);
    });
});
