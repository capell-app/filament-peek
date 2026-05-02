<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Data;

use Spatie\LaravelData\Data;

final class ExtensionAcquisitionData extends Data
{
    public function __construct(
        public string $composerName,
        public string $versionConstraint,
        public string $composerCommand,
        public ?string $repositoryUrl,
        public ?string $purchaseUrl,
        public bool $requiresDeployment,
    ) {}
}
