<?php

declare(strict_types=1);

namespace Capell\PublicActions\Data;

use Spatie\LaravelData\Data;

class PublicActionPayloadData extends Data
{
    /**
     * @param  array<string, mixed>  $values
     */
    public function __construct(
        public array $values = [],
    ) {}
}
