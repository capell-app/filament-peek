<?php

declare(strict_types=1);

namespace Capell\Layout\Enums;

use Capell\Layout\Filament\Resources\Pages\RelationManagers\ContentsRelationManager;
use Capell\Layout\Filament\Resources\Widgets\RelationManagers\WidgetAssetsRelationManager;
use Capell\Layout\Livewire\Assets\Table\ContentAssetsTable;
use Capell\Layout\Livewire\Assets\Table\PageAssetsTable;
use Capell\Layout\Livewire\Layout\WidgetTableSelect;
use Capell\Layout\Livewire\LayoutBuilder;
use Capell\Layout\Livewire\Widget\PagesWidget;

enum LivewireComponentsEnum: string
{
    case LayoutBuilder = 'capell.layout.livewire.layout-builder';
    case ContentsRelationManager = 'capell.layout.filament.resources.page-resource.relation-managers.contents-relation-manager';
    case WidgetAssetsRelationManager = 'capell.layout.filament.resources.widget-resource.relation-managers.widget-assets-relation-manager';
    case WidgetTableSelect = 'capell.layout.livewire.layout.widget-table-select';
    case PageAssetsTable = 'capell.layout.livewire.assets.table.page';
    case ContentAssetsTable = 'capell.layout.livewire.assets.table.content';
    case PagesWidget = 'capell.layout.livewire.widget.pages';

    public static function getComponents(): array
    {
        $components = [];
        foreach (self::cases() as $widgetComponent) {
            $components[$widgetComponent->value] = $widgetComponent->getComponent();
        }

        return $components;
    }

    public function getComponent(): ?string
    {
        return match ($this) {
            self::LayoutBuilder => LayoutBuilder::class,
            self::ContentsRelationManager => ContentsRelationManager::class,
            self::WidgetAssetsRelationManager => WidgetAssetsRelationManager::class,
            self::WidgetTableSelect => WidgetTableSelect::class,
            self::PageAssetsTable => PageAssetsTable::class,
            self::ContentAssetsTable => ContentAssetsTable::class,
            self::PagesWidget => PagesWidget::class,
        };
    }
}
