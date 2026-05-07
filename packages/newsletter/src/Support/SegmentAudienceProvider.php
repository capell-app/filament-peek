<?php

declare(strict_types=1);

namespace Capell\Newsletter\Support;

use Capell\Newsletter\Contracts\NewsletterAudienceProvider;
use Capell\Newsletter\Models\Segment;
use Illuminate\Support\Collection;

class SegmentAudienceProvider implements NewsletterAudienceProvider
{
    public function audiencesForSite(int $siteId): Collection
    {
        return Segment::query()
            ->where('site_id', $siteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
