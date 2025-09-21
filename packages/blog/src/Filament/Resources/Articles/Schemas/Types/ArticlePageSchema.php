<?php

declare(strict_types=1);

namespace Capell\Blog\Filament\Resources\Articles\Schemas\Types;

use Capell\Admin\Filament\Components\Forms\FixedWidthSidebar;
use Capell\Admin\Filament\Components\Forms\Media\MediaLibraryFileUpload;
use Capell\Admin\Filament\Components\Forms\Page\CreatePageSchema;
use Capell\Admin\Filament\Components\Forms\Page\LayoutSelect;
use Capell\Admin\Filament\Components\Forms\Page\PagePublishSection;
use Capell\Admin\Filament\Components\Forms\Page\PageSettingsSchema;
use Capell\Admin\Filament\Components\Forms\Page\PageSiteSelect;
use Capell\Admin\Filament\Components\Forms\Page\ParentPageSelect;
use Capell\Admin\Filament\Components\Forms\PublishSchema;
use Capell\Admin\Filament\Resources\Pages\Schemas\Types\DefaultPageSchema;
use Capell\Blog\Filament\Components\Forms\Page\PageTagsInput;
use Capell\Blog\Services\Loader\BlogLoader;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Override;

class ArticlePageSchema extends DefaultPageSchema
{
    #[Override]
    protected static function getFormSchema(Schema $schema): array
    {
        return [
            static::getTranslationFormSchema($schema),
            Section::make()
                ->contained(in_array($schema->getOperation(), ['create', 'edit']))
                ->columns()
                ->columnSpanFull()
                ->schema(static::getCreateExtraFor($schema)),
        ];
    }

    #[Override]
    protected static function getEditFormSchema(Schema $schema): array
    {
        return [
            FixedWidthSidebar::make()
                ->mainSchema([
                    static::getTranslationFormSchema($schema),
                ])
                ->sidebarSchema(
                    PageSettingsSchema::make(
                        $schema,
                        components: [
                            PageTagsInput::make('tags'),
                        ],
                        pageGroup: $schema->getLivewire()->getResource()::getResourceName(),
                        modifyParentQueryUsing: static::modifyParentQueryUsing($schema),
                        withType: false,
                    ),
                    contained: true
                ),
            Tabs::make()
                ->columnSpanFull()
                ->tabs(static::getTabs($schema)),
        ];
    }

    #[Override]
    protected static function getEditOptionFormSchema(Schema $schema): array
    {
        return [
            static::getTranslationFormSchema($schema),
            Section::make(__('capell-admin::generic.settings'))
                ->compact()
                ->schema([
                    ...PageSettingsSchema::make(
                        $schema,
                        components: [
                            PageTagsInput::make('tags'),
                            MediaLibraryFileUpload::make('image')
                                ->imageDefaults(),
                        ],
                        pageGroup: $schema->getLivewire()->getResource()::getResourceName(),
                        modifyParentQueryUsing: static::modifyParentQueryUsing($schema),
                        withType: false,
                    ),
                    PagePublishSection::make(),
                ]),
        ];
    }

    #[Override]
    protected static function getCreateExtraFor(Schema $schema): array
    {
        return [
            PageSiteSelect::make(),
            static::getParentPageSelect($schema),
            LayoutSelect::make('layout_id')
                ->reactive()
                ->withEditLink(),
            PublishSchema::make($schema),
        ];
    }

    #[Override]
    protected static function getParentPageSelect(Schema $schema): Select
    {
        return ParentPageSelect::make('parent_id')
            ->label(__('capell-admin::form.parent_page'))
            ->setupRelation('parent', $schema)
            ->pageGroup(static::modifyParentQueryUsing($schema))
            ->reactive();
    }

    protected static function modifyParentQueryUsing(Schema $schema): Closure
    {
        return function (Builder $query) use ($schema) {
            $site = $schema->getLivewire()->getSite();

            $blogPage = $site ? BlogLoader::getBlogPage($site) : null;

            return $query->adminResource(
                $schema->getLivewire()->getResource()::getResourceName()
            )
                ->when(
                    $blogPage,
                    fn (Builder $query) => $query->orWhere('id', 'blog')
                );
        };
    }
}
