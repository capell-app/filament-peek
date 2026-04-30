<?php

declare(strict_types=1);

namespace Capell\Forms\Providers;

use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\Forms\Models\Form;
use Capell\Forms\Models\Submission;
use Spatie\LaravelPackageTools\Package;

class FormsServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-forms';

    public static string $packageName = 'capell-app/forms';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('capell-forms')
            ->hasTranslations()
            ->hasMigrations([
                'create_forms_table',
                'create_submissions_table',
                'encrypt_submission_payload_and_meta',
            ]);
    }

    public function packageRegistered(): void
    {
        $this
            ->registerPackageMetadata()
            ->registerModels();
    }

    private function registerPackageMetadata(): self
    {
        CapellCore::registerPackage(
            self::$packageName,
            type: self::getType(),
            serviceProviderClass: self::class,
            path: realpath(__DIR__ . '/../..'),
            version: CapellCore::getInstalledPrettyVersion(self::$packageName),
            description: fn (): string => __('capell-forms::package.description'),
        );

        return $this;
    }

    private function registerModels(): self
    {
        CapellCore::registerModels([
            Form::class,
            Submission::class,
        ]);

        return $this;
    }
}
