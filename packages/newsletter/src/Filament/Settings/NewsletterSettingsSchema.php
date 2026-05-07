<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Settings;

use Capell\Admin\Filament\Contracts\HasSchema;
use Capell\Newsletter\Enums\ResubscribePolicy;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class NewsletterSettingsSchema implements HasSchema
{
    public static function make(Schema $configurator): array
    {
        return [
            Grid::make(2)
                ->columnSpanFull()
                ->schema([
                    Select::make('default_resubscribe_policy')
                        ->label(__('capell-newsletter::form.default_resubscribe_policy'))
                        ->options(self::resubscribePolicyOptions())
                        ->required(),
                    KeyValue::make('site_resubscribe_policies')
                        ->label(__('capell-newsletter::form.site_resubscribe_policies')),
                ]),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function resubscribePolicyOptions(): array
    {
        return collect(ResubscribePolicy::cases())
            ->mapWithKeys(static fn (ResubscribePolicy $policy): array => [$policy->value => $policy->getLabel()])
            ->all();
    }
}
