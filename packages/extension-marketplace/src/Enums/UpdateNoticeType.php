<?php

declare(strict_types=1);

namespace Capell\ExtensionMarketplace\Enums;

enum UpdateNoticeType: string
{
    case Update = 'update';
    case Bug = 'bug';
    case Security = 'security';
}
