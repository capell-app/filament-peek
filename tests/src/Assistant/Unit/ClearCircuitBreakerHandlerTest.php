<?php

declare(strict_types=1);
use Capell\Admin\Support\AdminEventRegistry;
use Capell\Admin\Support\AdminEventRouter;
use Capell\Assistant\Handlers\ClearCircuitBreakerHandler;
use Capell\Assistant\Support\OpenAIProvider;
use Filament\Notifications\Notification;
use Illuminate\Container\Container;

it('registers clear-circuit-breaker handler for EditPage and executes', function (): void {
    $container = new Container;

    $mockProvider = new class extends OpenAIProvider
    {
        public bool $resetCalled = false;

        public function resetCircuitBreaker(): void
        {
            $this->resetCalled = true;
        }
    };
    $container->instance(OpenAIProvider::class, $mockProvider);

    $container->bind(ClearCircuitBreakerHandler::class, fn (): ClearCircuitBreakerHandler => new ClearCircuitBreakerHandler);

    $router = new AdminEventRouter($container, $container->make(AdminEventRegistry::class));

    $component = new HandlerDummyComponent;
    $router->handle('clear-circuit-breaker', [], $component);

    expect($mockProvider->resetCalled)->toBeTrue();
    Notification::assertNotified();
});
