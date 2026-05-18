<?php

declare(strict_types=1);

namespace Capell\AccessGate\Tests\Fixtures\Autoload;

use Capell\AccessGate\Contracts\RegistrationField;
use Capell\AccessGate\Data\RegistrationFieldValue;
use Illuminate\Support\Facades\Validator;

final class PublicRequestProviderField implements RegistrationField
{
    public function key(): string
    {
        return 'provider_username';
    }

    public function label(): string
    {
        return 'Provider username';
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function validate(array $input): RegistrationFieldValue
    {
        $validated = Validator::make($input, [
            'provider_username' => ['required', 'string'],
        ])->validate();

        return new RegistrationFieldValue(
            key: $this->key(),
            value: strtolower((string) $validated['provider_username']),
        );
    }
}
