<?php

declare(strict_types=1);

use Capell\PublishingStudio\Providers\ConsoleServiceProvider;
use Capell\PublishingStudio\Providers\PublishingStudioServiceProvider;

it('publishing-studio service provider class exists in new namespace', function (): void {
    expect(class_exists(PublishingStudioServiceProvider::class))->toBeTrue();
});

it('discovers the console provider for install-time commands', function (): void {
    $composer = json_decode((string) file_get_contents(dirname(__DIR__, 3) . '/composer.json'), true);
    $provider = file_get_contents(dirname(__DIR__, 3) . '/src/Providers/ConsoleServiceProvider.php');

    expect($composer['extra']['laravel']['providers'])
        ->toContain(PublishingStudioServiceProvider::class)
        ->toContain(ConsoleServiceProvider::class)
        ->and($provider)
        ->toContain('$this->commands([')
        ->not->toContain('if ($this->app->runningInConsole()) {' . PHP_EOL . '            $this->commands([');
});

it('does not advertise a redundant manifest install command', function (): void {
    $manifest = json_decode((string) file_get_contents(dirname(__DIR__, 3) . '/capell.json'), true);

    expect($manifest['commands']['install'] ?? null)->toBeNull();
});
