<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Data;

use Capell\SeoSuite\Enums\AiDiscoveryCrawlerDirectiveEnum;
use Capell\SeoSuite\Enums\AiDiscoveryCrawlerPurposeEnum;
use Spatie\LaravelData\Data;

class AiDiscoveryCrawlerRuleData extends Data
{
    public function __construct(
        public string $provider,
        public string $userAgent,
        public AiDiscoveryCrawlerPurposeEnum $purpose,
        public AiDiscoveryCrawlerDirectiveEnum $directive,
        public string $path,
        public bool $enabled,
        public ?string $sourceUrl = null,
        public ?string $notes = null,
    ) {}
}
