<?php

declare(strict_types=1);

namespace Capell\Newsletter\Enums;

use Filament\Support\Contracts\HasLabel;

enum ConsentEventType: string implements HasLabel
{
    case FormCapture = 'form_capture';
    case DoubleOptInRequested = 'double_opt_in_requested';
    case DoubleOptInConfirmed = 'double_opt_in_confirmed';
    case Unsubscribed = 'unsubscribed';
    case Imported = 'imported';
    case AdminUpdated = 'admin_updated';
    case ProviderWebhook = 'provider_webhook';

    public function getLabel(): string
    {
        return __('capell-newsletter::generic.consent_event_type.' . $this->value);
    }
}
