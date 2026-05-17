<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Element\Page;

use Capell\Core\Enums\PageOrderEnum;
use Capell\Frontend\Facades\Frontend;
use Capell\Frontend\Support\Loader\PageLoader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class Latest extends AbstractPagesElement
{
    protected static string $defaultView = 'capell-foundation-theme::components.element.asset.pages';

    protected function mountElement(): void
    {
        $morphModel = $this->element->getMeta('page_model');

        $modelClass = null;

        if ($morphModel !== null) {
            $modelClass = Relation::getMorphedModel($morphModel);
        }

        $this->pages = PageLoader::getPages(
            language: Frontend::language(),
            site: Frontend::site(),
            page: Frontend::page(),
            limit: $this->element->meta['limit'] ?? config('capell-frontend.pagination_limit', 12),
            ordering: PageOrderEnum::Latest,
            pageGroup: $this->element->meta['page_group'] ?? null,
            withChildrenCount: $this->element->meta['with_children_count'] ?? false,
            withImage: $this->element->meta['with_image'] ?? false,
            withParent: $this->element->meta['with_parent'] ?? false,
            withDate: $this->element->meta['with_date'] ?? false,
            cacheKeyPrepend: 'latest-element-' . $this->element->id,
            morphModel: $modelClass,
            useCache: false,
            modifyQuery: fn (Builder $query) => $query->whereKeyNot(Frontend::page()->id),
        );

        if ($this->pages->isEmpty() && config('capell-layout-builder.element.skip_render_empty', true) === true) {
            $this->skipRender = true;
        }
    }
}
