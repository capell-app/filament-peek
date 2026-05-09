<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Enums;

use Filament\Support\Contracts\HasLabel;

enum AiDiscoveryCrawlerPurposeEnum: string implements HasLabel
{
    case Search = 'search';
    case Training = 'training';
    case UserTriggered = 'user_triggered';
    case AdsSafety = 'ads_safety';
    case GenericCrawl = 'generic_crawl';
    case Unknown = 'unknown';

    public function getLabel(): string
    {
        return __('capell-seo-suite::generic.ai_discovery_crawler_purpose_' . $this->value);
    }
}
