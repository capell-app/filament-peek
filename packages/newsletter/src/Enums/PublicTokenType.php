<?php

declare(strict_types=1);

namespace Capell\Newsletter\Enums;

enum PublicTokenType: string
{
    case Confirm = 'confirm';
    case Unsubscribe = 'unsubscribe';
}
