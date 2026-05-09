<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Enums;

use Filament\Support\Contracts\HasLabel;

enum AiDiscoveryCrawlerDirectiveEnum: string implements HasLabel
{
    case Allow = 'allow';
    case Disallow = 'disallow';

    public function getLabel(): string
    {
        return __('capell-seo-suite::generic.ai_discovery_crawler_directive_' . $this->value);
    }
}
