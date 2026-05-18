<?php

declare(strict_types=1);

namespace Capell\AccessGate\Tests\Fixtures\Autoload;

use Capell\AccessGate\Contracts\RegistrationField;
use Capell\AccessGate\Data\RegistrationFieldValue;
use Illuminate\Support\Facades\Validator;

final class TestProviderUsernameRegistrationField implements RegistrationField
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
            'provider_username' => ['required', 'string', 'alpha_dash:ascii'],
        ])->validate();

        $username = strtolower((string) $validated['provider_username']);

        return new RegistrationFieldValue(
            key: $this->key(),
            value: $username,
            metadata: [
                'avatar_url' => sprintf('https://avatars.example.test/%s.png', $username),
            ],
        );
    }
}
