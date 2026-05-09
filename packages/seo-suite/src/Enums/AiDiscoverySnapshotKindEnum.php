<?php

declare(strict_types=1);

namespace Capell\SeoSuite\Enums;

use Filament\Support\Contracts\HasLabel;

enum AiDiscoverySnapshotKindEnum: string implements HasLabel
{
    case LlmsTxt = 'llms_txt';
    case LlmsFullTxt = 'llms_full_txt';
    case PageMarkdown = 'page_markdown';

    public function getLabel(): string
    {
        return __('capell-seo-suite::generic.ai_discovery_snapshot_kind_' . $this->value);
    }
}
