<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Data;

use Spatie\LaravelData\Data;

final class InstalledPackageData extends Data
{
    public function __construct(
        public string $name,
        public string $label,
        public ?string $version,
        public ?string $path,
    ) {}
}
