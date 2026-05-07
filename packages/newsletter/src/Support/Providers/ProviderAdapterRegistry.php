<?php

declare(strict_types=1);

namespace Capell\Newsletter\Support\Providers;

use Capell\Newsletter\Contracts\NewsletterProviderAdapter;
use Capell\Newsletter\Enums\ProviderType;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class ProviderAdapterRegistry
{
    /**
     * @param  array<string, class-string<NewsletterProviderAdapter>>  $adapters
     */
    public function __construct(
        private Container $container,
        private array $adapters = [],
    ) {}

    /**
     * @param  class-string<NewsletterProviderAdapter>  $adapterClass
     */
    public function register(ProviderType $provider, string $adapterClass): void
    {
        $this->adapters[$provider->value] = $adapterClass;
    }

    public function resolve(ProviderType $provider): NewsletterProviderAdapter
    {
        $adapterClass = $this->adapters[$provider->value] ?? null;

        if (! is_string($adapterClass) || ! class_exists($adapterClass)) {
            throw new InvalidArgumentException(sprintf('Newsletter provider adapter [%s] is not registered.', $provider->value));
        }

        $adapter = $this->container->make($adapterClass);

        if (! $adapter instanceof NewsletterProviderAdapter) {
            throw new InvalidArgumentException(sprintf('Newsletter provider adapter [%s] is invalid.', $adapterClass));
        }

        return $adapter;
    }
}
