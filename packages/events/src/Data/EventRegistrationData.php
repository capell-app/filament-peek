<?php

declare(strict_types=1);

namespace Capell\Events\Data;

use Spatie\LaravelData\Data;

class EventRegistrationData extends Data
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public int $quantity = 1,
        public array $payload = [],
        public ?int $formSubmissionId = null,
    ) {}
}
