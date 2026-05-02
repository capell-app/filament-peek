<?php

declare(strict_types=1);

namespace Capell\Deployments\Data;

use Spatie\LaravelData\Data;

final class PullRequestData extends Data
{
    public function __construct(
        public readonly int|string $id,
        public readonly string $url,
        public readonly string $state,
        public readonly string $headBranch,
        public readonly string $baseBranch,
        public readonly string $headSha,
        public readonly bool $merged,
    ) {}
}
