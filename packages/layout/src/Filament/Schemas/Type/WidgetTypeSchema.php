<?php

declare(strict_types=1);

namespace Capell\Layout\Filament\Schemas\Type;

use Capell\Admin\Filament\Components\Forms\Widget\WidgetAdminSchema;
use Capell\Admin\Filament\Components\Forms\Widget\WidgetComponentFilesSection;
use Capell\Admin\Filament\Components\Forms\Widget\WidgetDisplaySection;
use Capell\Admin\Filament\Schemas\Type\AbstractTypeSchema;
use Filament\Forms;

class WidgetTypeSchema extends AbstractTypeSchema
{
    public static function make(Forms\Form $form): array
    {
        return [
            Forms\Components\Tabs::make()
                ->tabs([
                    self::getFrontendTab(),
                    self::getAdminTab(),
                ]),
        ];
    }

    private static function getAdminTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('capell-admin::generic.admin'))
            ->statePath('admin')
            ->icon('heroicon-m-wrench-screwdriver')
            ->columns()
            ->schema(WidgetAdminSchema::make());
    }

    private static function getFrontendTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('capell-admin::generic.frontend'))
            ->statePath('meta')
            ->icon('heroicon-m-building-storefront')
            ->columns(3)
            ->schema([
                WidgetDisplaySection::make(),
                WidgetComponentFilesSection::make(componentRequired: true),
            ]);
    }
}
