<?php

declare(strict_types=1);

namespace Capell\Newsletter\Data;

use Capell\Newsletter\Enums\ConfirmationMode;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class FormMappingData extends Data
{
    /**
     * @param  array<int, int>  $fixedTagIds
     * @param  array<string, array<string, int>>  $fieldTagMappings
     */
    public function __construct(
        public int $siteId,
        public string $emailField,
        public ?int $formId = null,
        public ?string $formHandle = null,
        public ?string $firstNameField = null,
        public ?string $lastNameField = null,
        public ?string $consentField = null,
        public ?string $consentText = null,
        public ?string $consentVersion = null,
        public array $fixedTagIds = [],
        public array $fieldTagMappings = [],
        public bool $requiresDoubleOptIn = true,
        public ConfirmationMode $confirmationMode = ConfirmationMode::CapellOwned,
    ) {}
}
