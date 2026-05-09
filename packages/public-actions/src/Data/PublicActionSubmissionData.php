<?php

declare(strict_types=1);

namespace Capell\PublicActions\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PublicActionSubmissionData extends Data
{
    public function __construct(
        public string $actionKey,
        public PublicActionPayloadData $payload,
        public PublicActionMetadataData $metadata,
        public ?string $sourceType = null,
        public ?string $sourceId = null,
    ) {}
}
