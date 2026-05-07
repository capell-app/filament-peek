<?php

declare(strict_types=1);

namespace Capell\Newsletter\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ProviderSyncResultData extends Data
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public bool $successful,
        public ?string $remoteId = null,
        public ?string $remoteStatus = null,
        public ?string $errorMessage = null,
        public array $payload = [],
    ) {}
}
