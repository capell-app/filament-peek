<?php

declare(strict_types=1);

namespace Capell\PublicActions\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PublicActionResultData extends Data
{
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public ?string $redirectUrl = null,
        public ?string $createdModelType = null,
        public ?string $createdModelId = null,
    ) {}
}
