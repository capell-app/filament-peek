<?php

declare(strict_types=1);

namespace Capell\Redirects\Providers;

use Capell\Core\Contracts\Redirects\RedirectUrlRecorder;
use Capell\Core\Events\PageSaved;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\Redirects\Contracts\RedirectRecorder;
use Capell\Redirects\Contracts\RedirectResolver;
use Capell\Redirects\Listeners\CreateRedirectsForChangedPageUrls;
use Capell\Redirects\Support\FrontendRedirectResolver;
use Capell\Redirects\Support\PageUrlRedirectRecorder;
use Capell\Redirects\Support\PageUrlRedirectResolver;
use Capell\Redirects\Support\RedirectsPackageUrlRecorder;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;

class RedirectsServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'redirects';

    public static string $packageName = 'capell-app/redirects';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasConfigFile('redirects')
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        CapellCore::registerPackage(
            static::$packageName,
            serviceProviderClass: static::class,
            path: realpath(__DIR__ . '/../..'),
            version: CapellCore::getInstalledPrettyVersion(static::$packageName),
            description: fn (): string => __('redirects::package.description'),
        );

        $this->app->booted(function (): void {
            if (! $this->isPackageInstalled()) {
                return;
            }

            $this->registerInstalledPackage();
        });
    }

    private function isPackageInstalled(): bool
    {
        return CapellCore::isPackageInstalled(static::$packageName);
    }

    private function registerInstalledPackage(): void
    {
        $this->app->singleton(RedirectResolver::class, PageUrlRedirectResolver::class);
        $this->app->singleton(RedirectRecorder::class, PageUrlRedirectRecorder::class);

        if (interface_exists(RedirectUrlRecorder::class)) {
            $this->app->singleton(RedirectUrlRecorder::class, RedirectsPackageUrlRecorder::class);
        }

        if (interface_exists(\Capell\Frontend\Contracts\RedirectResolver::class)) {
            $this->app->singleton(\Capell\Frontend\Contracts\RedirectResolver::class, FrontendRedirectResolver::class);
        }

        Event::listen(PageSaved::class, [CreateRedirectsForChangedPageUrls::class, 'handle']);
    }
}
