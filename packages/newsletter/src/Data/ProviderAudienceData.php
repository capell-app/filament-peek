<?php

declare(strict_types=1);

namespace Capell\Newsletter\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ProviderAudienceData extends Data
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function __construct(
        public string $remoteId,
        public string $name,
        public array $settings = [],
    ) {}
}
