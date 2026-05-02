<?php

declare(strict_types=1);

namespace Capell\Deployments\Data;

use Spatie\LaravelData\Data;

final class RepoFile extends Data
{
    public function __construct(
        public readonly string $path,
        public readonly string $content,
        public readonly ?string $sha = null,
    ) {}
}
