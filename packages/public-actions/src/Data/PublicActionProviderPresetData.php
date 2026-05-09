<?php

declare(strict_types=1);

namespace Capell\PublicActions\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PublicActionProviderPresetData extends Data
{
    public function __construct(
        public string $key,
        public string $adapter,
        public string $method,
        public bool $expectsJson,
    ) {}
}
