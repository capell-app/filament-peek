<?php

declare(strict_types=1);

namespace Capell\Newsletter\Support;

use Capell\Newsletter\Contracts\NewsletterAudienceProvider;
use Illuminate\Support\Collection;

class NewsletterAudienceRegistry
{
    /**
     * @var array<int, NewsletterAudienceProvider>
     */
    private array $providers = [];

    public function register(NewsletterAudienceProvider $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * @return Collection<int, mixed>
     */
    public function audiencesForSite(int $siteId): Collection
    {
        return collect($this->providers)
            ->flatMap(static fn (NewsletterAudienceProvider $provider): Collection => $provider->audiencesForSite($siteId))
            ->values();
    }
}
