<?php

declare(strict_types=1);

use Capell\SeoSuite\Exceptions\OpenAICircuitBreakerOpenException;
use Capell\SeoSuite\Support\Cache\AIGenerationCache;
use Capell\SeoSuite\Support\Cache\RateLimitCache;
use Capell\SeoSuite\Support\PrismProvider;
use Illuminate\Support\Facades\Cache;
use Prism\Prism\Enums\Provider;

it('stores ai generation values behind the configured cache driver and ttl', function (): void {
    Cache::flush();

    $cache = new AIGenerationCache('array', 120);
    $calls = 0;

    $remembered = $cache->remember('brief:1', function () use (&$calls): string {
        $calls++;

        return 'generated brief';
    });
    $secondRemembered = $cache->remember('brief:1', function () use (&$calls): string {
        $calls++;

        return 'new brief';
    });

    $cache->put('brief:2', 'stored brief', 60);

    expect($remembered)->toBe('generated brief')
        ->and($secondRemembered)->toBe('generated brief')
        ->and($calls)->toBe(1)
        ->and($cache->get('brief:2'))->toBe('stored brief')
        ->and($cache->get('missing', 'fallback'))->toBe('fallback')
        ->and($cache->keyFor('page', 42))->toBe('page:42')
        ->and($cache->ttl())->toBe(120);
});

it('stores rate limit state behind the configured cache driver', function (): void {
    Cache::flush();

    $cache = new RateLimitCache('array');
    $key = $cache->keyFor('user-7');

    $cache->put($key, ['attempts' => 2], 30);

    expect($key)->toBe('ai_rate_limit_user-7')
        ->and($cache->get($key))->toBe(['attempts' => 2])
        ->and($cache->ttl())->toBe(60);

    $cache->forget($key);

    expect($cache->get($key, 'empty'))->toBe('empty');
});

it('maps provider aliases and exposes service metadata', function (): void {
    $provider = new PrismProvider(['max_retries' => 1, 'retry_delay_ms' => 0]);

    expect($provider->handles())->toBe('prism_provider')
        ->and($provider->isAvailable())->toBeTrue();
});

it('resolves configured prism provider names to prism enums', function (string $name, Provider $expected): void {
    $provider = new PrismProvider;
    $resolveProvider = new ReflectionMethod(PrismProvider::class, 'resolveProvider');

    expect($resolveProvider->invoke($provider, $name))->toBe($expected);
})->with([
    'anthropic' => ['anthropic', Provider::Anthropic],
    'gemini' => ['gemini', Provider::Gemini],
    'google' => ['google', Provider::Gemini],
    'ollama' => ['ollama', Provider::Ollama],
    'openai fallback' => ['unknown', Provider::OpenAI],
]);

it('opens and resets the prism circuit breaker after repeated failures', function (): void {
    Cache::flush();

    $provider = new PrismProvider;
    $recordFailure = new ReflectionMethod(PrismProvider::class, 'recordFailure');

    expect($provider->isAvailable())->toBeTrue();

    for ($failure = 1; $failure <= 5; $failure++) {
        $recordFailure->invoke($provider);
    }

    expect($provider->isAvailable())->toBeFalse()
        ->and(fn (): mixed => $provider->chat(['messages' => []]))->toThrow(OpenAICircuitBreakerOpenException::class);

    $provider->resetCircuitBreaker();

    expect($provider->isAvailable())->toBeTrue();
});
