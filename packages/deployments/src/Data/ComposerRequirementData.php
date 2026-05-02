<?php

declare(strict_types=1);

namespace Capell\Deployments\Data;

use Spatie\LaravelData\Data;

final class ComposerRequirementData extends Data
{
    public function __construct(
        public string $composerName,
        public string $versionConstraint = '*',
        public ?string $repositoryUrl = null,
        public ?string $label = null,
    ) {}
}
