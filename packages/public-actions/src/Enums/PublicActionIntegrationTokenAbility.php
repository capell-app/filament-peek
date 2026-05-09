<?php

declare(strict_types=1);

namespace Capell\PublicActions\Enums;

use Filament\Support\Contracts\HasLabel;

enum PublicActionIntegrationTokenAbility: string implements HasLabel
{
    case ListActions = 'list_actions';
    case SubmitActions = 'submit_actions';
    case ReadSubmissions = 'read_submissions';

    public function getLabel(): string
    {
        return __("capell-public-actions::generic.statuses.integration_ability.{$this->value}");
    }
}
