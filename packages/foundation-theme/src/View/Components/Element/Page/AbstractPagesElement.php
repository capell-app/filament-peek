<?php

declare(strict_types=1);

namespace Capell\FoundationTheme\View\Components\Element\Page;

use Capell\FoundationTheme\View\Components\Element\AbstractElement;
use Capell\Frontend\Actions\GetPageVariablesAction;
use Capell\Frontend\Facades\Frontend;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

abstract class AbstractPagesElement extends AbstractElement
{
    public ?string $componentItem = null;

    public Collection $pages;

    protected static string $defaultView = 'capell-layout-builder::components.element.asset.pages';

    public function render(array $data = []): View|string|Closure
    {
        if ($this->skipRender && config('capell-layout-builder.element.skip_render_empty', true) === true) {
            return '';
        }

        $page = Frontend::page();
        $title = $this->element->translation?->title;
        $content = '';

        if ($title !== null && $title !== '') {
            $content .= '<div class="element-content">' . e(__($title, GetPageVariablesAction::run($page))) . '</div>';
        }

        if ($this->pages->isEmpty()) {
            $content .= '<div class="no-results">' . e(__('capell-layout-builder::generic.no_pages_found')) . '</div>';
        } else {
            foreach ($this->pages as $pageItem) {
                $itemTitle = $pageItem->translation?->title ?? $pageItem->name;
                $itemUrl = $pageItem->pageUrl?->full_url ?? '#';

                $content .= '<article class="' . e($this->element->key) . '-page-item">';
                $content .= '<a href="' . e($itemUrl) . '">' . e($itemTitle) . '</a>';
                $content .= '</article>';
            }
        }

        return '<section class="element element-' . e($this->element->key) . ' element-pages">' . $content . '</section>';
    }
}
