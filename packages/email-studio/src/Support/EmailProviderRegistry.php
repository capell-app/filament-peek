<?php

declare(strict_types=1);

namespace Capell\EmailStudio\Support;

use Capell\EmailStudio\Contracts\EmailProviderAdapter;
use Capell\EmailStudio\Enums\EmailProviderType;
use Capell\EmailStudio\Exceptions\EmailStudioSendingException;

class EmailProviderRegistry
{
    /** @var array<string, EmailProviderAdapter> */
    private array $adapters = [];

    public function register(EmailProviderType $provider, EmailProviderAdapter $adapter): self
    {
        $this->adapters[$provider->value] = $adapter;

        return $this;
    }

    public function adapter(EmailProviderType $provider): EmailProviderAdapter
    {
        return $this->adapters[$provider->value]
            ?? throw EmailStudioSendingException::providerNotRegistered($provider->value);
    }

    /**
     * @return array<int, string>
     */
    public function supportedProviders(): array
    {
        return array_keys($this->adapters);
    }
}
