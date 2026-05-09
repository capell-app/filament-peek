<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Data;

use Spatie\LaravelData\Data;

class AiDiscoveryAuditData extends Data
{
    public function __construct(
        public ?int $pageId,
        public string $checkKey,
        public string $severity,
        public string $message,
        public bool $passed,
    ) {}
}
