<?php

declare(strict_types=1);

namespace Capell\Newsletter\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ProviderInterestData extends Data
{
    public function __construct(
        public int $tagId,
        public string $remoteId,
        public ?string $remoteType = null,
        public ?string $name = null,
    ) {}
}
