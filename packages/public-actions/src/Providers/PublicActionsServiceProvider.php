<?php

declare(strict_types=1);

namespace Capell\PublicActions\Providers;

use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\PublicActions\Models\PublicAction;
use Capell\PublicActions\Models\PublicActionDestination;
use Capell\PublicActions\Models\PublicActionDispatchAttempt;
use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Capell\PublicActions\Models\PublicActionSubmission;
use Spatie\LaravelPackageTools\Package;

class PublicActionsServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-public-actions';

    public static string $packageName = 'capell-app/public-actions';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasConfigFile('capell-public-actions')
            ->hasTranslations()
            ->hasRoute('web')
            ->hasMigrations([
                '01_create_public_actions_table',
                '02_create_public_action_destinations_table',
                '03_create_public_action_submissions_table',
                '04_create_public_action_dispatch_attempts_table',
                '05_create_public_action_integration_tokens_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->registerPackageMetadata();

        $this->app->booted(function (): void {
            if (! $this->isPackageInstalled()) {
                return;
            }

            $this
                ->registerModels()
                ->registerAdminResources()
                ->registerProtectedTables();
        });
    }

    private function isPackageInstalled(): bool
    {
        return CapellCore::isPackageInstalled(self::$packageName);
    }

    private function registerPackageMetadata(): self
    {
        CapellCore::registerPackage(
            self::$packageName,
            type: self::getType(),
            serviceProviderClass: self::class,
            path: realpath(__DIR__ . '/../..'),
            version: CapellCore::getInstalledPrettyVersion(self::$packageName),
            description: fn (): string => __('capell-public-actions::package.description'),
        );

        return $this;
    }

    private function registerModels(): self
    {
        CapellCore::registerModels([
            PublicAction::class,
            PublicActionDestination::class,
            PublicActionSubmission::class,
            PublicActionDispatchAttempt::class,
            PublicActionIntegrationToken::class,
        ]);

        return $this;
    }

    private function registerAdminResources(): self
    {
        return $this;
    }

    private function registerProtectedTables(): self
    {
        $tables = config('capell-public-actions.tables', []);

        if (! is_array($tables)) {
            return $this;
        }

        foreach ($tables as $tableName) {
            if (! is_string($tableName) || $tableName === '') {
                continue;
            }

            CapellCore::registerProtectedTable(static fn (): string => $tableName);
        }

        return $this;
    }
}
