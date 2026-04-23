<?php

declare(strict_types=1);

namespace Capell\SeoTools\Enums;

use Capell\SeoTools\Models\AiCreatorContext;
use Capell\SeoTools\Models\AiCreatorSession;
use Capell\SeoTools\Models\AIGenerationHistory;

enum ModelEnum: string
{
    case AIGenerationHistory = AIGenerationHistory::class;
    case AiCreatorContext = AiCreatorContext::class;
    case AiCreatorSession = AiCreatorSession::class;
}
