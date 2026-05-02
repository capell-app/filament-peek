<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Actions;

use Capell\ExtensionMarketplace\Data\ExtensionAcquisitionData;
use Capell\ExtensionMarketplace\Data\ExtensionListingData;
use Capell\ExtensionMarketplace\Services\MarketplaceClient;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateExtensionAcquisitionAction
{
    use AsAction;

    public function __construct(private readonly MarketplaceClient $marketplace) {}

    /**
     * @param  array<string, mixed>  $installOptions
     */
    public function handle(
        ExtensionListingData $listing,
        ?string $licenseKey = null,
        ?string $email = null,
        ?string $domain = null,
        array $installOptions = [],
    ): ExtensionAcquisitionData {
        $resolvedEmail = $email ?? auth()->user()?->email ?? 'unknown@local';
        $configuredDomain = parse_url((string) config('app.url'), PHP_URL_HOST);
        $resolvedDomain = $domain ?? (is_string($configuredDomain) && $configuredDomain !== '' ? $configuredDomain : 'localhost');
        $selectedInstallOptions = $this->selectedInstallOptions($listing, $installOptions);

        $authorization = $this->marketplace->createInstallAuthorization(
            slug: $listing->slug,
            licenseKey: $licenseKey,
            email: $resolvedEmail,
            domain: $resolvedDomain,
            installOptions: $selectedInstallOptions,
        );

        $composerName = $authorization->composerName ?? $listing->composerName;
        $versionConstraint = $authorization->versionConstraint !== '' ? $authorization->versionConstraint : ($listing->latestVersion !== null ? '^' . $listing->latestVersion : '*');
        $repositoryUrl = $authorization->repositoryUrl ?? $listing->forkRepoUrl;

        return new ExtensionAcquisitionData(
            composerName: $composerName,
            versionConstraint: $versionConstraint,
            composerCommand: sprintf('composer require %s:%s', $composerName, $versionConstraint),
            repositoryUrl: $repositoryUrl,
            purchaseUrl: $listing->purchaseUrl,
            requiresDeployment: $repositoryUrl !== null,
        );
    }

    /**
     * @param  array<string, mixed>  $installOptions
     * @return array<string, mixed>
     */
    private function selectedInstallOptions(ExtensionListingData $listing, array $installOptions): array
    {
        $allowedKeys = [];

        foreach ($listing->installOptions as $option) {
            $key = $option['key'] ?? null;

            if (is_string($key) && $key !== '') {
                $allowedKeys[$key] = true;
            }
        }

        return $allowedKeys === [] ? [] : array_intersect_key($installOptions, $allowedKeys);
    }
}
