<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Data;

use Capell\Core\Models\Language;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\SiteDomain;
use Spatie\LaravelData\Data;

class AiDiscoveryRenderContextData extends Data
{
    public function __construct(
        public Site $site,
        public Language $language,
        public ?SiteDomain $siteDomain = null,
        public ?Page $page = null,
    ) {}

    public function domainKey(): string
    {
        return $this->siteDomain?->getDomainKey() ?? 'default';
    }
}
