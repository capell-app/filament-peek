<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Data;

use Spatie\LaravelData\Data;

class SeoCheckData extends Data
{
    public function __construct(
        public string $label,
        public bool $pass,
        public ?string $detail = null,
        public ?string $icon = null,
        public ?string $tooltip = null,
    ) {}
}
