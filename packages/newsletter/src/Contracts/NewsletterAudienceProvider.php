<?php

declare(strict_types=1);

namespace Capell\Newsletter\Contracts;

use Illuminate\Support\Collection;

interface NewsletterAudienceProvider
{
    /**
     * @return Collection<int, mixed>
     */
    public function audiencesForSite(int $siteId): Collection;
}
