<?php

declare(strict_types=1);

use Capell\Assistant\Support\OpenAIProvider;

it('runs test openai connection command successfully', function (): void {
    // Bind a fake provider to avoid external calls and force a healthy response
    $fakeProvider = new class([]) extends OpenAIProvider
    {
        public function isAvailable(): bool
        {
            return true;
        }
    };

    app()->instance(OpenAIProvider::class, $fakeProvider);

    $this->artisan('capell-admin:test-openai')
        ->assertExitCode(0);
});
