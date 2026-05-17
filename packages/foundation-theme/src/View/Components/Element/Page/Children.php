<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Element\Page;

use Capell\Core\Enums\PageOrderEnum;
use Capell\Frontend\Facades\Frontend;
use Capell\Frontend\Support\Loader\PageLoader;
use Capell\Frontend\Support\Logging\FrontendLogger;

class Children extends AbstractPagesElement
{
    protected static string $defaultView = 'capell-foundation-theme::components.element.asset.pages';

    protected function mountElement(): void
    {
        $page = Frontend::page();

        if (! $page->hasPageHierarchy()) {
            $logger = resolve(FrontendLogger::class);

            $logger->warning('Frontend: page has no page hierarchy for children element', [
                'pageable_type' => $page->getMorphClass(),
                'pageable_id' => $page->getKey(),
                'layout_id' => $page->layout->key,
            ]);

            $this->skipRender = true;

            return;
        }

        if (isset($page->type->meta['hidden']) && $page->type->meta['hidden'] === true) {
            $this->skipRender = true;

            return;
        }

        $this->pages = PageLoader::getPages(
            language: Frontend::language(),
            site: Frontend::site(),
            page: Frontend::page(),
            type: 'children',
            ordering: PageOrderEnum::Alphabetical,
            withChildrenCount: $this->element->meta['with_children_count'] ?? false,
            withImage: $this->element->meta['with_image'] ?? false,
            withParent: $this->element->meta['with_parent'] ?? false,
            withDate: $this->element->meta['with_date'] ?? false,
            useCache: false,
        );

        if ($this->pages->isEmpty()) {
            $this->skipRender = true;
        }
    }
}
