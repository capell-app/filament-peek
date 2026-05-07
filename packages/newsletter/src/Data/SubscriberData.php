<?php

declare(strict_types=1);

namespace Capell\Newsletter\Data;

use Capell\Newsletter\Enums\SubscriberStatus;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class SubscriberData extends Data
{
    /**
     * @param  array<string, mixed>  $profile
     */
    public function __construct(
        public int $siteId,
        public string $email,
        public SubscriberStatus $status = SubscriberStatus::Pending,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public array $profile = [],
        public ?int $sourceFormId = null,
        public ?string $sourceFormHandle = null,
    ) {}
}
