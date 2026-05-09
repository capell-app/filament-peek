<?php

declare(strict_types=1);

namespace Capell\PublicActions\Data;

use Capell\PublicActions\Models\PublicActionIntegrationToken;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PublicActionIntegrationTokenData extends Data
{
    /**
     * @param  list<string>  $abilities
     */
    public function __construct(
        public string $plainTextToken,
        public PublicActionIntegrationToken $token,
        public array $abilities,
    ) {}
}
