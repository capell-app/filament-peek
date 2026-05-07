<?php

declare(strict_types=1);

namespace Capell\Newsletter\Enums;

use Filament\Support\Contracts\HasLabel;

enum ResubscribePolicy: string implements HasLabel
{
    case RequireDoubleOptIn = 'require_double_opt_in';
    case AllowWithConsent = 'allow_with_consent';
    case BlockSuppressedOnly = 'block_suppressed_only';

    public function getLabel(): string
    {
        return __('capell-newsletter::generic.resubscribe_policy.' . $this->value);
    }
}
