<?php

declare(strict_types=1);

namespace Capell\DocumentLifecycle\Enums;

enum DocumentStatusEnum: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
