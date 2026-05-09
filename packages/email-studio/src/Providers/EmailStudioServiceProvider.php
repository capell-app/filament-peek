<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Providers;

use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Spatie\LaravelPackageTools\Package;

class EmailStudioServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-email-studio';

    public static string $packageName = 'capell-app/email-studio';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasConfigFile('capell-email-studio')
            ->hasTranslations()
            ->hasRoute('web')
            ->hasMigrations([
                '01_create_email_profiles_table',
                '02_create_email_templates_table',
                '03_create_email_template_variants_table',
                '04_create_email_messages_table',
                '05_create_email_recipients_table',
                '06_create_email_events_table',
                '07_create_email_replies_table',
                '08_create_email_suppressions_table',
                '09_create_email_template_registrations_table',
                '10_create_email_tracking_tokens_table',
            ]);
    }

    public function registeringPackage(): void
    {
        $this->app->register(AdminServiceProvider::class);
        $this->app->register(FrontendServiceProvider::class);
    }

    public function packageRegistered(): void
    {
        $this->registerPackageMetadata();
    }

    private function registerPackageMetadata(): self
    {
        CapellCore::registerPackage(
            self::$packageName,
            type: self::getType(),
            serviceProviderClass: self::class,
            path: realpath(__DIR__ . '/../..'),
            version: CapellCore::getInstalledPrettyVersion(self::$packageName),
            description: fn (): string => __('capell-email-studio::package.description'),
        );

        return $this;
    }
}
