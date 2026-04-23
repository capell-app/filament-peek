<?php

declare(strict_types=1);

namespace Capell\DefaultTheme\Filament\Schemas\Themes;

use Capell\Admin\Contracts\SchemaTypeEnumInterface;
use Capell\Admin\Contracts\TypeSchemaInterface;
use Capell\Admin\Enums\SchemaExtenderEnum;
use Capell\Admin\Enums\SchemaTypeEnum;
use Capell\Admin\Filament\Components\Forms\DefaultToggle;
use Capell\Admin\Filament\Components\Forms\IconPicker;
use Capell\Admin\Filament\Components\Forms\MediaLibraryFileUpload;
use Capell\Admin\Filament\Components\Forms\StatusToggle;
use Capell\Admin\Filament\Concerns\HasTypeSchema;
use Capell\DefaultTheme\Filament\Components\Forms\Theme\AssetsSchema;
use Capell\DefaultTheme\Filament\Components\Forms\Theme\ColorsSchema;
use Capell\DefaultTheme\Filament\Components\Forms\Theme\DetailsSchema;
use Capell\DefaultTheme\Filament\Components\Forms\Theme\FontsSchema;
use Capell\DefaultTheme\Filament\Components\Forms\Theme\FooterFieldset;
use Capell\DefaultTheme\Filament\Components\Forms\Theme\HeaderFieldset;
use Capell\DefaultTheme\Filament\Components\Forms\Theme\MainFieldset;
use Capell\DefaultTheme\Filament\Components\Forms\Theme\TypeSelect;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DefaultThemeSchema implements TypeSchemaInterface
{
    use HasTypeSchema;

    public static SchemaTypeEnumInterface $schemaType = SchemaTypeEnum::Theme;

    public static function getExtenders(): iterable
    {
        return app()->tagged(SchemaExtenderEnum::Theme->value);
    }

    public function make(Schema $schema): array
    {
        return [
            ...DetailsSchema::make($schema),
            TypeSelect::make('type_id')
                ->live()
                ->withRelation()
                ->when(
                    $schema->isCreating(),
                    fn (TypeSelect $component): TypeSelect => $component->withCreateForm(),
                    fn (TypeSelect $component): TypeSelect => $component->withEditForm(),
                ),
            Tabs::make()
                ->columnSpanFull()
                ->schema([
                    $this->colorsTab(),
                    $this->fontsTab(),
                    $this->layoutTab(),
                    $this->assetsTab(),
                    $this->settingsTab($schema),
                    $this->frontendTab(),
                ]),
            Grid::make()
                ->columnSpanFull()
                ->schema([
                    DefaultToggle::make('default'),
                    StatusToggle::make('status')
                        ->inline(),
                ]),
        ];
    }

    protected function colorsTab(): Tab
    {
        return Tab::make(__('capell-admin::tab.colors'))
            ->key('colors')
            ->statePath('meta')
            ->icon(config('capell-admin.icon.colors', Heroicon::OutlinedPaintBrush))
            ->gridContainer()
            ->columns(['@sm' => 2])
            ->schema(ColorsSchema::make());
    }

    protected function fontsTab(): Tab
    {
        return Tab::make(__('capell-admin::tab.fonts'))
            ->key('fonts')
            ->statePath('meta')
            ->icon(config('capell-admin.icon.fonts', Heroicon::OutlinedDocumentText))
            ->gridContainer()
            ->columns(['@sm' => 2])
            ->schema(FontsSchema::make());
    }

    protected function layoutTab(): Tab
    {
        return Tab::make(__('capell-admin::tab.layout'))
            ->key('layout')
            ->statePath('meta')
            ->icon(Heroicon::OutlinedPuzzlePiece)
            ->columns(['@lg' => 2])
            ->gridContainer()
            ->schema([
                Grid::make(['@sm' => 2])
                    ->gridContainer()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('container')
                            ->label(__('capell-admin::form.container'))
                            ->options([
                                'sm' => __('capell-admin::generic.sm'),
                                'md' => __('capell-admin::generic.md'),
                                'lg' => __('capell-admin::generic.lg'),
                            ]),
                    ]),
                HeaderFieldset::make()->columnSpanFull(),
                MainFieldset::make(),
                FooterFieldset::make(),
            ]);
    }

    protected function assetsTab(): Tab
    {
        return Tab::make(__('capell-admin::tab.assets'))
            ->key('assets')
            ->statePath('meta')
            ->icon(Heroicon::OutlinedFolderOpen)
            ->gridContainer()
            ->columns(['@sm' => 2])
            ->schema(AssetsSchema::make());
    }

    protected function settingsTab(Schema $schema): Tab
    {
        return Tab::make(__('capell-admin::tab.settings'))
            ->key('settings')
            ->statePath('meta')
            ->icon(Heroicon::OutlinedCog6Tooth)
            ->columns(['@sm' => 2])
            ->gridContainer()
            ->schema([
                TextInput::make('main_class')
                    ->label(__('capell-admin::form.main_class')),
                Checkbox::make('rounded_images')
                    ->label(__('capell-admin::form.rounded_images'))
                    ->inline(),
                $this->customCssSection(),
                CodeEditor::make('meta_tags')
                    ->label(__('capell-admin::form.meta_tags'))
                    ->columnSpanFull(),
            ]);
    }

    protected function frontendTab(): Tab
    {
        return Tab::make(__('capell-admin::tab.admin'))
            ->key('admin')
            ->statePath('admin')
            ->icon(config('capell-admin.icon.admin'))
            ->gridContainer()
            ->columns(['@sm' => 2])
            ->schema([
                IconPicker::make('icon'),
                MediaLibraryFileUpload::make('image')
                    ->label(__('capell-admin::form.preview_image')),
            ]);
    }

    protected function customCssSection(): Section
    {
        return Section::make(__('capell-admin::generic.custom_css'))
            ->icon(Heroicon::OutlinedCodeBracket)
            ->compact()
            ->collapsed()
            ->columnSpanFull()
            ->schema([
                CodeEditor::make('custom_css')
                    ->label(__('capell-admin::form.custom_css'))
                    ->hiddenLabel(),
            ]);
    }
}
