<?php

declare(strict_types=1);

namespace Capell\PublicActions\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PublicActionMetadataData extends Data
{
    public function __construct(
        public ?string $ipHash = null,
        public ?string $userAgent = null,
        public ?string $url = null,
        public ?string $referer = null,
        public ?string $route = null,
        public ?int $siteId = null,
    ) {}
}
