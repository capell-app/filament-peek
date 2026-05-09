<?php

declare(strict_types=1);

namespace Capell\PublicActions\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PublicActionZapierSubmissionData extends Data
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $id,
        public string $actionKey,
        public string $submittedAt,
        public array $payload,
        public ?string $siteName = null,
        public ?string $sourceType = null,
    ) {}
}
