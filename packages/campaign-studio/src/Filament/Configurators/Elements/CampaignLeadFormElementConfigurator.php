<?php

declare(strict_types=1);

namespace Capell\CampaignStudio\Filament\Configurators\Elements;

use Capell\LayoutBuilder\Filament\Configurators\Elements\DefaultElementConfigurator;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;

final class CampaignLeadFormElementConfigurator extends DefaultElementConfigurator
{
    protected function detailsTab(): Tab
    {
        return Tab::make('campaign_form')
            ->label(__('capell-campaign-studio::form.form'))
            ->schema([
                TextInput::make('meta.form_handle')
                    ->label(__('capell-campaign-studio::form.form')),
                TextInput::make('meta.goal_key')
                    ->label(__('capell-campaign-studio::form.primary_goal')),
            ]);
    }
}
