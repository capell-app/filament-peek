<?php

declare(strict_types=1);

use Capell\PublicActions\Support\PublicActionProviderPresetRegistry;
use Illuminate\Support\Facades\Config;

it('resolves default provider presets to the generic http webhook adapter', function (): void {
    $registry = resolve(PublicActionProviderPresetRegistry::class);

    foreach (['generic', 'zapier', 'pipedream', 'n8n', 'make'] as $presetKey) {
        $preset = $registry->get($presetKey);

        expect($preset)->not->toBeNull()
            ->and($preset?->key)->toBe($presetKey)
            ->and($preset?->adapter)->toBe('http_webhook')
            ->and($preset?->method)->toBe('POST')
            ->and($preset?->expectsJson)->toBeTrue();
    }
});

it('normalizes malformed preset configuration conservatively', function (): void {
    Config::set('capell-public-actions.adapters.presets.custom', [
        'adapter' => '',
        'method' => 'put',
        'expects_json' => false,
    ]);

    $preset = resolve(PublicActionProviderPresetRegistry::class)->get('custom');

    expect($preset?->adapter)->toBe('http_webhook')
        ->and($preset?->method)->toBe('PUT')
        ->and($preset?->expectsJson)->toBeFalse();
});

it('binds the provider preset registry as a singleton', function (): void {
    expect(resolve(PublicActionProviderPresetRegistry::class))->toBe(resolve(PublicActionProviderPresetRegistry::class));
});
