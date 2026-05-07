<?php

declare(strict_types=1);

namespace Capell\Newsletter\Data;

use Capell\Newsletter\Enums\SubscriberStatus;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ProviderSubscriberData extends Data
{
    /**
     * @param  array<string, mixed>  $profile
     * @param  array<int, ProviderInterestData>  $interests
     */
    public function __construct(
        public string $email,
        public SubscriberStatus $status,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public array $profile = [],
        public array $interests = [],
        public ?string $remoteId = null,
    ) {}
}
