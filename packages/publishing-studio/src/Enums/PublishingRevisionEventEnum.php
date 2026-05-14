<?php

declare(strict_types=1);

namespace Capell\PublishingStudio\Enums;

enum PublishingRevisionEventEnum: string
{
    case Published = 'published';
    case Restored = 'restored';
}
